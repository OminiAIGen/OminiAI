<?php

declare(strict_types=1);

namespace App\Extensions\PhoneCallAgent\System\Services;

use App\Extensions\PhoneCallAgent\System\Enums\DirectionEnum;
use App\Extensions\PhoneCallAgent\System\Models\ExtPhoneCallAgentCall;
use App\Models\User;

class PhoneCallAgentLimitService
{
    public const UNLIMITED = -1;

    public function __construct(private readonly User $user) {}

    /**
     * Whether the user may receive an inbound call under their plan's seconds budget.
     * Mirrors the historical inbound gate, now scoped to inbound usage only.
     */
    public function canReceiveInboundCall(): bool
    {
        $limit = (int) ($this->user->activePlan()?->phone_call_agent_seconds_limit ?? self::UNLIMITED);

        if ($limit === self::UNLIMITED) {
            return true;
        }

        if ($limit === 0) {
            return false;
        }

        return $this->monthlySeconds(DirectionEnum::Inbound) < $limit;
    }

    /**
     * Whether the user may place an outbound call. Checks the outbound seconds budget
     * and the per-day outbound call cap independently.
     *
     * @return array{allowed: bool, reason: string|null}
     */
    public function canPlaceOutboundCall(): array
    {
        if (! $this->isOutboundEnabled()) {
            return ['allowed' => false, 'reason' => __('phone-call-agent::phone-call-agent.outbound_not_on_plan')];
        }

        $plan = $this->user->activePlan();

        $secondsLimit = (int) ($plan?->phone_call_agent_outbound_seconds_limit ?? self::UNLIMITED);

        if ($secondsLimit === 0) {
            return ['allowed' => false, 'reason' => __('phone-call-agent::phone-call-agent.outbound_not_on_plan')];
        }

        if ($secondsLimit > 0 && $this->monthlySeconds(DirectionEnum::Outbound) >= $secondsLimit) {
            return ['allowed' => false, 'reason' => __('phone-call-agent::phone-call-agent.outbound_seconds_reached')];
        }

        $dailyCap = (int) ($plan?->phone_call_agent_outbound_calls_per_day ?? self::UNLIMITED);

        if ($dailyCap === 0) {
            return ['allowed' => false, 'reason' => __('phone-call-agent::phone-call-agent.outbound_not_on_plan')];
        }

        if ($dailyCap > 0 && $this->todayOutboundCallCount() >= $dailyCap) {
            return ['allowed' => false, 'reason' => __('phone-call-agent::phone-call-agent.outbound_daily_cap_reached')];
        }

        return ['allowed' => true, 'reason' => null];
    }

    /**
     * Whether the user's plan permits placing outbound calls at all.
     * Defaults to allowed when the plan predates the flag.
     */
    public function isOutboundEnabled(): bool
    {
        return (bool) ($this->user->activePlan()?->phone_call_agent_outbound_enabled ?? true);
    }

    /**
     * Whether the user's plan permits scheduling automatic follow-up / callback calls.
     * A follow-up is itself an outbound call, so outbound must also be enabled.
     * Defaults to allowed when the plan predates the flags.
     */
    public function canUseFollowUp(): bool
    {
        return $this->isOutboundEnabled()
            && (bool) ($this->user->activePlan()?->phone_call_agent_follow_up_enabled ?? true);
    }

    /**
     * Remaining outbound seconds for the month. Null when unlimited.
     */
    public function remainingOutboundSeconds(): ?int
    {
        $limit = (int) ($this->user->activePlan()?->phone_call_agent_outbound_seconds_limit ?? self::UNLIMITED);

        if ($limit <= self::UNLIMITED) {
            return null;
        }

        return max(0, $limit - $this->monthlySeconds(DirectionEnum::Outbound));
    }

    /**
     * Remaining outbound calls for today. Null when unlimited.
     */
    public function remainingOutboundCallsToday(): ?int
    {
        $cap = (int) ($this->user->activePlan()?->phone_call_agent_outbound_calls_per_day ?? self::UNLIMITED);

        if ($cap <= self::UNLIMITED) {
            return null;
        }

        return max(0, $cap - $this->todayOutboundCallCount());
    }

    private function monthlySeconds(DirectionEnum $direction): int
    {
        return (int) ExtPhoneCallAgentCall::query()
            ->where('user_id', $this->user->getKey())
            ->where('direction', $direction->value)
            ->whereMonth('started_at', now()->month)
            ->whereYear('started_at', now()->year)
            ->sum('duration');
    }

    /**
     * Outbound calls actually placed today (dialed rows have a started_at; pending/scheduled rows do not).
     */
    private function todayOutboundCallCount(): int
    {
        return (int) ExtPhoneCallAgentCall::query()
            ->where('user_id', $this->user->getKey())
            ->where('direction', DirectionEnum::Outbound->value)
            ->whereNotNull('started_at')
            ->whereDate('started_at', today())
            ->count();
    }
}
