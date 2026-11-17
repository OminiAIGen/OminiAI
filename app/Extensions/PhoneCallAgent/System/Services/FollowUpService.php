<?php

declare(strict_types=1);

namespace App\Extensions\PhoneCallAgent\System\Services;

use App\Extensions\PhoneCallAgent\System\Enums\CallSourceEnum;
use App\Extensions\PhoneCallAgent\System\Enums\CallStatusEnum;
use App\Extensions\PhoneCallAgent\System\Enums\FollowUpTriggerEnum;
use App\Extensions\PhoneCallAgent\System\Enums\FollowUpUnitEnum;
use App\Extensions\PhoneCallAgent\System\Models\ExtPhoneCallAgent;
use App\Extensions\PhoneCallAgent\System\Models\ExtPhoneCallAgentBooking;
use App\Extensions\PhoneCallAgent\System\Models\ExtPhoneCallAgentCall;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class FollowUpService
{
    public function __construct(private readonly PhoneCallAgentService $service) {}

    /**
     * Queue the booking-relative follow-up calls (reminders / post-appointment surveys) defined by the
     * agent's follow-up rules. Only enabled before/after-booking rules with a future computed time and a
     * reachable customer number produce a call.
     */
    public function scheduleForBooking(ExtPhoneCallAgentBooking $booking): void
    {
        $agent = $booking->agent;

        if (! $agent || ! $agent->phone_number || ! $booking->customer_phone || ! $this->planAllows($agent)) {
            return;
        }

        foreach ($this->rules($agent, [FollowUpTriggerEnum::BeforeBooking, FollowUpTriggerEnum::AfterBooking]) as $rule) {
            $minutes = $this->offsetMinutes($rule);

            $scheduledAt = $rule['trigger'] === FollowUpTriggerEnum::BeforeBooking->value
                ? optional($booking->starts_at)->copy()?->subMinutes($minutes)
                : optional($booking->ends_at ?? $booking->starts_at)->copy()?->addMinutes($minutes);

            if (! $scheduledAt || $scheduledAt->isPast()) {
                continue;
            }

            $this->createFollowUpCall($agent, $booking->customer_phone, $scheduledAt, $rule, $booking);
        }
    }

    /**
     * Fire the on-cancel notification call (placed ASAP) and drop any still-pending follow-up calls for
     * a booking that was cancelled.
     */
    public function scheduleOnCancel(ExtPhoneCallAgentBooking $booking): void
    {
        $agent = $booking->agent;

        if (! $agent || ! $agent->phone_number || ! $booking->customer_phone || ! $this->planAllows($agent)) {
            return;
        }

        foreach ($this->rules($agent, [FollowUpTriggerEnum::OnCancel]) as $rule) {
            $this->createFollowUpCall($agent, $booking->customer_phone, null, $rule, $booking);
        }
    }

    /**
     * Cancel the still-pending follow-up calls generated from a booking (used on cancel / before a
     * reschedule re-queues them).
     */
    public function cancelForBooking(ExtPhoneCallAgentBooking $booking): void
    {
        $booking->calls()
            ->where('status', CallStatusEnum::Pending->value)
            ->update(['status' => CallStatusEnum::Cancelled->value]);
    }

    /**
     * Queue after-call survey callbacks once an inbound call has finished.
     */
    public function scheduleAfterCall(ExtPhoneCallAgentCall $call): void
    {
        $agent = $call->agent;

        if (! $agent || ! $agent->phone_number || ! $call->caller_number || ! $this->planAllows($agent)) {
            return;
        }

        $base = $call->ended_at ?? now();

        foreach ($this->rules($agent, [FollowUpTriggerEnum::AfterCall]) as $rule) {
            $scheduledAt = $base->copy()->addMinutes($this->offsetMinutes($rule));

            $this->createFollowUpCall($agent, $call->caller_number, $scheduledAt, $rule, null);
        }
    }

    /**
     * Whether the agent owner's plan permits follow-up calls.
     */
    private function planAllows(ExtPhoneCallAgent $agent): bool
    {
        $user = $agent->user;

        return $user !== null && (new PhoneCallAgentLimitService($user))->canUseFollowUp();
    }

    /**
     * @param  array<int, FollowUpTriggerEnum>  $triggers
     *
     * @return array<int, array<string, mixed>>
     */
    private function rules(ExtPhoneCallAgent $agent, array $triggers): array
    {
        $wanted = array_map(fn (FollowUpTriggerEnum $t): string => $t->value, $triggers);

        return array_values(array_filter(
            $agent->follow_up_rules ?? [],
            fn ($rule): bool => is_array($rule)
                && ($rule['enabled'] ?? false)
                && in_array($rule['trigger'] ?? null, $wanted, true)
        ));
    }

    /**
     * @param  array<string, mixed>  $rule
     */
    private function offsetMinutes(array $rule): int
    {
        $unit = FollowUpUnitEnum::tryFrom($rule['offset_unit'] ?? '') ?? FollowUpUnitEnum::Hour;

        return $unit->toMinutes(max(0, (int) ($rule['offset_value'] ?? 0)));
    }

    /**
     * @param  array<string, mixed>  $rule
     */
    private function createFollowUpCall(
        ExtPhoneCallAgent $agent,
        string $toNumber,
        ?Carbon $scheduledAt,
        array $rule,
        ?ExtPhoneCallAgentBooking $booking
    ): void {
        $call = $this->service->createOutboundCall(
            $agent,
            $toNumber,
            null,
            $scheduledAt,
            CallSourceEnum::FollowUp,
            $this->renderInstruction($rule['instruction'] ?? '', $booking),
            $booking?->id,
        );

        Log::info('[FollowUp] scheduled', [
            'call_id'      => $call->id,
            'agent_id'     => $agent->id,
            'trigger'      => $rule['trigger'] ?? null,
            'scheduled_at' => $scheduledAt?->toIso8601String(),
        ]);
    }

    /**
     * Substitute the booking placeholders ({name}, {time}, {date}) available to a follow-up instruction.
     */
    private function renderInstruction(string $instruction, ?ExtPhoneCallAgentBooking $booking): string
    {
        if ($instruction === '' || ! $booking) {
            return $instruction;
        }

        return strtr($instruction, [
            '{name}' => $booking->customer_name ?? '',
            '{time}' => $booking->starts_at?->format('H:i') ?? '',
            '{date}' => $booking->starts_at?->format('Y-m-d') ?? '',
        ]);
    }
}
