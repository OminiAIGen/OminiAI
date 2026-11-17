<?php

declare(strict_types=1);

namespace App\Extensions\PhoneCallAgent\System\Console\Commands;

use App\Extensions\PhoneCallAgent\System\Enums\CallStatusEnum;
use App\Extensions\PhoneCallAgent\System\Enums\DirectionEnum;
use App\Extensions\PhoneCallAgent\System\Jobs\PlaceOutboundCallJob;
use App\Extensions\PhoneCallAgent\System\Models\ExtPhoneCallAgentCall;
use App\Extensions\PhoneCallAgent\System\Models\ExtPhoneCallAgentCampaign;
use App\Extensions\PhoneCallAgent\System\Services\PhoneCallAgentLimitService;
use App\Helpers\Classes\Helper;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DispatchDueOutboundCalls extends Command
{
    /**
     * Proves this command itself ran, as opposed to the app-wide cron heartbeat ("crontab_check").
     * A live cron with a stale value here means the run is being blocked — a held overlap lock, or
     * the extension provider not booting in the cron process.
     */
    public const HEARTBEAT_KEY = 'phone_call_agent_dispatch_heartbeat';

    protected $signature = 'phone-call-agent:dispatch-due-outbound';

    protected $description = 'Dispatch due (immediate or scheduled) outbound phone calls, respecting per-plan daily caps.';

    public function handle(): int
    {
        // Recorded before every early return so a blocked run is distinguishable from a no-op run.
        Cache::put(self::HEARTBEAT_KEY, now(), now()->addDay());

        if (Helper::appIsDemo()) {
            Log::warning('[PhoneCallAgent] Outbound dispatch skipped: demo mode is on.');

            return self::SUCCESS;
        }

        $dueByUser = ExtPhoneCallAgentCall::query()
            ->where('direction', DirectionEnum::Outbound->value)
            ->where('status', CallStatusEnum::Pending->value)
            ->where(function ($query) {
                $query->whereNull('scheduled_at')->orWhere('scheduled_at', '<=', now());
            })
            ->orderBy('id')
            ->get()
            ->groupBy('user_id');

        $due = $dueByUser->flatten()->count();
        $dispatched = 0;

        Log::debug('[PhoneCallAgent] Outbound dispatch scan', [
            'due_calls' => $due,
            'users'     => $dueByUser->keys()->all(),
        ]);

        foreach ($dueByUser as $userId => $calls) {
            $user = User::find($userId);

            if (! $user) {
                Log::warning('[PhoneCallAgent] Due outbound calls have no owning user and can never dial', [
                    'user_id'  => $userId,
                    'call_ids' => $calls->pluck('id')->all(),
                ]);

                continue;
            }

            $limits = app(PhoneCallAgentLimitService::class, ['user' => $user]);
            $remaining = $limits->remainingOutboundCallsToday();

            $dispatchable = $remaining === null ? $calls : $calls->take($remaining);

            // A remaining count below the due count silently withholds rows, which is indistinguishable
            // from "the scheduler never ran" unless it is reported here.
            if ($remaining !== null && $remaining < $calls->count()) {
                $withheld = $calls->skip($remaining);

                Log::warning('[PhoneCallAgent] Outbound calls withheld by the daily call cap', [
                    'user_id'           => $userId,
                    'due_calls'         => $calls->count(),
                    'remaining_today'   => $remaining,
                    'withheld_call_ids' => $withheld->pluck('id')->all(),
                    'outbound_enabled'  => $limits->isOutboundEnabled(),
                ]);

                $this->markWithheld($withheld);
            }

            foreach ($dispatchable as $call) {
                Log::debug('[PhoneCallAgent] Dispatching outbound call', [
                    'call_id'      => $call->id,
                    'agent_id'     => $call->agent_id,
                    'scheduled_at' => $call->scheduled_at?->toDateTimeString(),
                ]);

                PlaceOutboundCallJob::dispatch($call);
                $dispatched++;
            }
        }

        if ($due > 0) {
            Log::info('[PhoneCallAgent] Outbound dispatch finished', [
                'due_calls'  => $due,
                'dispatched' => $dispatched,
                'withheld'   => $due - $dispatched,
            ]);
        }

        $this->reapStuckCalls();
        $this->syncCampaignStatuses();

        return self::SUCCESS;
    }

    /**
     * A row withheld here is never handed to PlaceOutboundCallJob, so nothing else would ever record
     * why it stayed pending. Written only when it changes, so a row capped for days does not churn
     * updated_at on every tick.
     *
     * @param  Collection<int, ExtPhoneCallAgentCall>  $calls
     */
    private function markWithheld(Collection $calls): void
    {
        $reason = __('phone-call-agent::phone-call-agent.outbound_daily_cap_reached');

        ExtPhoneCallAgentCall::query()
            ->whereIn('id', $calls->pluck('id'))
            ->where(function ($query) use ($reason): void {
                $query->whereNull('status_reason')->orWhere('status_reason', '!=', $reason);
            })
            ->update(['status_reason' => $reason]);
    }

    /**
     * Recover rows that took the Queued claim in PlaceOutboundCallJob but can never progress: the
     * worker died before dialling (no started_at), or the call went out and the provider's status
     * webhook never arrived. Both otherwise sit as "In Progress" in the UI forever.
     */
    private function reapStuckCalls(): void
    {
        $undialedCutoff = now()->subMinutes((int) config('phone-call-agent.outbound.stuck_undialed_minutes', 15));

        $undialed = ExtPhoneCallAgentCall::query()
            ->where('direction', DirectionEnum::Outbound->value)
            ->where('status', CallStatusEnum::Queued->value)
            ->whereNull('started_at')
            ->where('updated_at', '<=', $undialedCutoff)
            ->get();

        $this->failStuck($undialed, 'outbound_call_never_dialed', 'Outbound calls claimed but never dialled');

        $dialedCutoff = now()->subMinutes((int) config('phone-call-agent.outbound.stuck_dialed_minutes', 60));

        $dialed = ExtPhoneCallAgentCall::query()
            ->where('direction', DirectionEnum::Outbound->value)
            ->whereIn('status', [
                CallStatusEnum::Queued->value,
                CallStatusEnum::Ringing->value,
                CallStatusEnum::Answered->value,
            ])
            ->whereNotNull('started_at')
            ->whereNull('ended_at')
            ->where('started_at', '<=', $dialedCutoff)
            ->get();

        $this->failStuck($dialed, 'outbound_call_no_status_callback', 'Outbound calls placed but never finalized by the provider');
    }

    /**
     * @param  Collection<int, ExtPhoneCallAgentCall>  $calls
     */
    private function failStuck(Collection $calls, string $langKey, string $message): void
    {
        if ($calls->isEmpty()) {
            return;
        }

        Log::warning("[PhoneCallAgent] {$message}", [
            'call_ids' => $calls->pluck('id')->all(),
        ]);

        foreach ($calls as $call) {
            $call->update([
                'status'        => CallStatusEnum::Failed->value,
                'status_reason' => __("phone-call-agent::phone-call-agent.{$langKey}"),
            ]);
        }
    }

    private function syncCampaignStatuses(): void
    {
        ExtPhoneCallAgentCampaign::query()
            ->whereIn('status', ['pending', 'running'])
            ->each(function (ExtPhoneCallAgentCampaign $campaign): void {
                $pending = $campaign->calls()->where('status', CallStatusEnum::Pending->value)->exists();

                $campaign->update(['status' => $pending ? 'running' : 'completed']);
            });
    }
}
