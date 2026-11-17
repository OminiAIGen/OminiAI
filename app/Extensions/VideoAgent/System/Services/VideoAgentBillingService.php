<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Services;

use App\Domains\Entity\Enums\EntityEnum;
use App\Domains\Entity\Facades\Entity;
use App\Enums\Introduction;
use App\Extensions\VideoAgent\System\Enums\VideoAgentKindEnum;
use App\Extensions\VideoAgent\System\Models\VideoAgentVideo;
use App\Models\User;
use App\Services\SharedCredit\SharedCreditService;
use Throwable;

/**
 * Video Agent bills on two layers: a monthly plan allowance (seconds and
 * videos) and per-second entity credits. Both are checked before a render is
 * submitted, and the credit debit happens once the real duration is known.
 */
class VideoAgentBillingService
{
    public function __construct(private readonly SharedCreditService $sharedCredits) {}

    /**
     * @return true|string true, or the reason the user cannot start a render
     */
    public function assertCanStart(User $user, VideoAgentKindEnum $kind, int $estimatedSeconds): true|string
    {
        $planAccess = $this->checkPlanAccess($user);

        if ($planAccess !== true) {
            return $planAccess;
        }

        $planLimit = $this->checkPlanLimits($user, $estimatedSeconds);

        if ($planLimit !== true) {
            return $planLimit;
        }

        return $this->checkCreditBalance($user, $kind, $estimatedSeconds);
    }

    /**
     * @return array{
     *     seconds_used: int, seconds_limit: int, seconds_unlimited: bool,
     *     videos_used: int, videos_limit: int, videos_unlimited: bool
     * }
     */
    public function usage(User $user): array
    {
        $plan = $user->relationPlan;

        $secondsLimit = (int) ($plan->video_agent_seconds_limit ?? -1);
        $videosLimit = (int) ($plan->video_agent_videos_limit ?? -1);

        return [
            'seconds_used'      => $this->secondsUsedThisMonth($user),
            'seconds_limit'     => $secondsLimit,
            'seconds_unlimited' => $secondsLimit === -1,
            'videos_used'       => $this->videosThisMonth($user),
            'videos_limit'      => $videosLimit,
            'videos_unlimited'  => $videosLimit === -1,
        ];
    }

    /**
     * Debit entity credits once, using the real rendered duration.
     */
    public function debit(VideoAgentVideo $video): void
    {
        if ($video->usage_deducted || $video->user === null) {
            return;
        }

        $seconds = max(1, (int) $video->duration_seconds);

        try {
            $driver = Entity::driver($video->kind->entity())
                ->forUser($video->user)
                ->inputSecond($seconds)
                ->calculateCredit();

            $driver->decreaseCredit();

            $video->forceFill([
                'credits'        => $driver->calculate(),
                'usage_deducted' => true,
            ])->save();
        } catch (Throwable $e) {
            report($e);
        }
    }

    private function checkPlanAccess(User $user): true|string
    {
        if ($user->isAdmin()) {
            return true;
        }

        $plan = $user->relationPlan;

        if (! $plan) {
            return __('You need an active plan to use Video Agent.');
        }

        if (! $plan->checkOpenAiItem(Introduction::VIDEO_AGENT->value)) {
            return __('Video Agent is not available in your current plan.');
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

        $videosLimit = (int) ($plan->video_agent_videos_limit ?? -1);

        if ($videosLimit === 0) {
            return __('Video Agent is not available in your current plan.');
        }

        if ($videosLimit > 0 && $this->videosThisMonth($user) >= $videosLimit) {
            return __('You have reached your monthly limit of :count Video Agent videos.', [
                'count' => $videosLimit,
            ]);
        }

        $secondsLimit = (int) ($plan->video_agent_seconds_limit ?? -1);

        if ($secondsLimit === -1) {
            return true;
        }

        if ($secondsLimit === 0) {
            return __('Video Agent is not available in your current plan.');
        }

        $used = $this->secondsUsedThisMonth($user);

        if ($used >= $secondsLimit) {
            return __('You have reached your monthly Video Agent limit of :seconds seconds.', [
                'seconds' => $secondsLimit,
            ]);
        }

        if ($estimatedSeconds > 0 && ($used + $estimatedSeconds) > $secondsLimit) {
            return __('This render would exceed your monthly Video Agent limit of :seconds seconds.', [
                'seconds' => $secondsLimit,
            ]);
        }

        return true;
    }

    private function checkCreditBalance(User $user, VideoAgentKindEnum $kind, int $estimatedSeconds): true|string
    {
        if ($user->isAdmin()) {
            return true;
        }

        try {
            $driver = Entity::driver($kind->entity())
                ->forUser($user)
                ->inputSecond(max(1, $estimatedSeconds))
                ->calculateCredit();

            if (! $driver->hasCreditBalance()) {
                return __('You do not have enough credits for :model.', [
                    'model' => $kind->entity()->label(),
                ]);
            }
        } catch (Throwable $e) {
            report($e);
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
        return (int) $this->monthlyVideos($user)->sum('duration_seconds');
    }

    private function videosThisMonth(User $user): int
    {
        return $this->monthlyVideos($user)->count();
    }

    private function monthlyVideos(User $user): \Illuminate\Database\Eloquent\Builder
    {
        return VideoAgentVideo::query()
            ->where('user_id', $user->id)
            ->billable()
            ->where('created_at', '>=', now()->startOfMonth());
    }

    /**
     * Rough pre-flight estimate: a spoken script runs at ~150 words per minute
     * and HeyGen bills per second, so the prompt length is the only signal we
     * have before the storyboard exists.
     */
    public function estimateSecondsFromPrompt(string $prompt): int
    {
        $words = max(1, str_word_count(strip_tags($prompt)));

        return (int) max(15, min(600, ceil($words / 150 * 60)));
    }

    public function entityFor(VideoAgentKindEnum $kind): EntityEnum
    {
        return $kind->entity();
    }
}
