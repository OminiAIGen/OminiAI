<?php

declare(strict_types=1);

namespace App\Extensions\AutoEditVideos\System\Services;

use App\Domains\Entity\Facades\Entity;
use App\Enums\Introduction;
use App\Extensions\AutoEditVideos\System\Enums\AutoEditKindEnum;
use App\Extensions\AutoEditVideos\System\Models\AutoEditRender;
use App\Models\User;
use App\Services\SharedCredit\SharedCreditService;
use Illuminate\Database\Eloquent\Builder;
use Throwable;

/**
 * Auto Edit Videos bills on two layers: a monthly plan allowance (seconds of
 * output and number of renders) and per-second entity credits.
 *
 * Both are checked before anything is submitted, and the credit debit happens
 * once — when a render completes and its real duration is known. A job that
 * fails or is canceled therefore never charges, which is why there is no refund
 * path anywhere in this extension.
 */
class AutoEditBillingService
{
    public function __construct(private readonly SharedCreditService $sharedCredits) {}

    /**
     * @return true|string true, or the reason the user cannot start
     */
    public function assertCanStart(User $user, int $estimatedSeconds): true|string
    {
        $planAccess = $this->checkPlanAccess($user);

        if ($planAccess !== true) {
            return $planAccess;
        }

        $planLimit = $this->checkPlanLimits($user, $estimatedSeconds);

        if ($planLimit !== true) {
            return $planLimit;
        }

        return $this->checkCreditBalance($user, $estimatedSeconds);
    }

    /**
     * @return array{
     *     seconds_used: int, seconds_limit: int, seconds_unlimited: bool,
     *     renders_used: int, renders_limit: int, renders_unlimited: bool
     * }
     */
    public function usage(User $user): array
    {
        $plan = $user->relationPlan;

        $secondsLimit = (int) ($plan->auto_edit_videos_seconds_limit ?? -1);
        $rendersLimit = (int) ($plan->auto_edit_videos_renders_limit ?? -1);

        return [
            'seconds_used'      => $this->secondsUsedThisMonth($user),
            'seconds_limit'     => $secondsLimit,
            'seconds_unlimited' => $secondsLimit === -1,
            'renders_used'      => $this->rendersThisMonth($user),
            'renders_limit'     => $rendersLimit,
            'renders_unlimited' => $rendersLimit === -1,
        ];
    }

    /**
     * Debit entity credits once, using the real rendered duration.
     */
    public function debit(AutoEditRender $render): void
    {
        if ($render->usage_deducted || $render->user === null) {
            return;
        }

        $seconds = max(1, (int) $render->duration_seconds);

        try {
            $driver = Entity::driver($render->kind->entity())
                ->forUser($render->user)
                ->inputSecond($seconds)
                ->calculateCredit();

            $driver->decreaseCredit();

            $render->forceFill([
                'credits'        => $driver->calculate(),
                'usage_deducted' => true,
            ])->save();
        } catch (Throwable $e) {
            report($e);
        }
    }

    /**
     * Whether the user could afford this render at the duration we expect.
     * Called again right before an export, when the cost is known exactly.
     */
    public function checkCreditBalance(User $user, int $estimatedSeconds): true|string
    {
        if ($user->isAdmin()) {
            return true;
        }

        try {
            $driver = Entity::driver(AutoEditKindEnum::AutoEdit->entity())
                ->forUser($user)
                ->inputSecond(max(1, $estimatedSeconds))
                ->calculateCredit();

            if (! $driver->hasCreditBalance()) {
                return __('You do not have enough credits for :model.', [
                    'model' => AutoEditKindEnum::AutoEdit->entity()->label(),
                ]);
            }
        } catch (Throwable $e) {
            report($e);
        }

        return true;
    }

    /**
     * Rough pre-flight estimate. Before Rendley has planned anything the only
     * signals are how long the footage runs and whether the user asked for a
     * target length; the target wins because it is a fact, not a guess.
     */
    public function estimateSeconds(?int $targetSeconds, int $sourceSeconds): int
    {
        if ($targetSeconds !== null && $targetSeconds > 0) {
            return $targetSeconds;
        }

        if ($sourceSeconds > 0) {
            // An auto edit is a condensation, so the output is a fraction of
            // the footage. Half is a deliberately generous floor for the gate.
            return (int) max(15, min(600, ceil($sourceSeconds / 2)));
        }

        return 60;
    }

    private function checkPlanAccess(User $user): true|string
    {
        if ($user->isAdmin()) {
            return true;
        }

        $plan = $user->relationPlan;

        if (! $plan) {
            return __('You need an active plan to use Auto Edit Videos.');
        }

        if (! $plan->checkOpenAiItem(Introduction::AUTO_EDIT_VIDEOS->value)) {
            return __('Auto Edit Videos is not available in your current plan.');
        }

        return true;
    }

    private function checkPlanLimits(User $user, int $estimatedSeconds): true|string
    {
        if ($this->bypassesPlanLimits($user)) {
            return true;
        }

        $plan = $user->relationPlan;

        if (! $plan) {
            return true;
        }

        $rendersLimit = (int) ($plan->auto_edit_videos_renders_limit ?? -1);

        if ($rendersLimit === 0) {
            return __('Auto Edit Videos is not available in your current plan.');
        }

        if ($rendersLimit > 0 && $this->rendersThisMonth($user) >= $rendersLimit) {
            return __('You have reached your monthly limit of :count auto edited videos.', [
                'count' => $rendersLimit,
            ]);
        }

        $secondsLimit = (int) ($plan->auto_edit_videos_seconds_limit ?? -1);

        if ($secondsLimit === -1) {
            return true;
        }

        if ($secondsLimit === 0) {
            return __('Auto Edit Videos is not available in your current plan.');
        }

        $used = $this->secondsUsedThisMonth($user);

        if ($used >= $secondsLimit) {
            return __('You have reached your monthly Auto Edit Videos limit of :seconds seconds.', [
                'seconds' => $secondsLimit,
            ]);
        }

        if ($estimatedSeconds > 0 && ($used + $estimatedSeconds) > $secondsLimit) {
            return __('This render would exceed your monthly Auto Edit Videos limit of :seconds seconds.', [
                'seconds' => $secondsLimit,
            ]);
        }

        return true;
    }

    private function bypassesPlanLimits(User $user): bool
    {
        return $user->isAdmin()
            || ($this->sharedCredits->isEnabled() && $user->isSharedCreditUser());
    }

    private function secondsUsedThisMonth(User $user): int
    {
        return (int) $this->monthlyRenders($user)->sum('duration_seconds');
    }

    private function rendersThisMonth(User $user): int
    {
        return $this->monthlyRenders($user)->count();
    }

    private function monthlyRenders(User $user): Builder
    {
        return AutoEditRender::query()
            ->where('user_id', $user->id)
            ->billable()
            ->where('created_at', '>=', now()->startOfMonth());
    }
}
