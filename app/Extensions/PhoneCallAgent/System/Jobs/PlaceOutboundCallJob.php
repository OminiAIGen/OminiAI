<?php

declare(strict_types=1);

namespace App\Extensions\PhoneCallAgent\System\Jobs;

use App\Extensions\PhoneCallAgent\System\Enums\CallStatusEnum;
use App\Extensions\PhoneCallAgent\System\Models\ExtPhoneCallAgentCall;
use App\Extensions\PhoneCallAgent\System\Services\PhoneCallAgentLimitService;
use App\Extensions\PhoneCallAgent\System\Services\PhoneCallAgentService;
use App\Helpers\Classes\Helper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class PlaceOutboundCallJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public ExtPhoneCallAgentCall $call) {}

    public function handle(PhoneCallAgentService $service): void
    {
        if (Helper::appIsDemo()) {
            Log::warning('[PhoneCallAgent] Outbound call not placed: demo mode is on.', [
                'call_id' => $this->call->getKey(),
            ]);

            return;
        }

        // Atomically claim the row so a duplicate dispatch (queue backlog / re-scan) can't double-dial.
        $claimed = ExtPhoneCallAgentCall::query()
            ->whereKey($this->call->getKey())
            ->where('status', CallStatusEnum::Pending->value)
            ->update(['status' => CallStatusEnum::Queued->value]);

        if (! $claimed) {
            Log::warning('[PhoneCallAgent] Outbound call claim lost; another worker owns it or it is no longer pending', [
                'call_id' => $this->call->getKey(),
                'status'  => $this->call->fresh()?->status?->value,
            ]);

            return;
        }

        $call = $this->call->fresh();

        if (! $call || ! $call->agent || ! $call->user) {
            Log::error('[PhoneCallAgent] Outbound call has a broken relation and cannot dial', [
                'call_id'       => $this->call->getKey(),
                'row_missing'   => $call === null,
                'agent_missing' => $call !== null && ! $call->agent,
                'user_missing'  => $call !== null && ! $call->user,
            ]);

            // The row is already claimed as Queued, so leaving it here would strand it forever with no
            // reason shown in the UI. Fail it instead.
            $call?->update([
                'status'        => CallStatusEnum::Failed->value,
                'status_reason' => __('phone-call-agent::phone-call-agent.outbound_call_broken_relation'),
            ]);

            return;
        }

        $decision = app(PhoneCallAgentLimitService::class, ['user' => $call->user])->canPlaceOutboundCall();

        if (! $decision['allowed']) {
            // Batch/scheduled rows defer (release the claim) so a later run can retry once budget frees up;
            // a lone immediate call can't linger, so it fails.
            if ($call->campaign_id || $call->scheduled_at) {
                $call->update([
                    'status' => CallStatusEnum::Pending->value,
                    // Reverting to pending with no reason is what makes a deferred row look identical to
                    // an untouched scheduled one in the UI. Cleared again on a successful dial.
                    'status_reason' => $decision['reason'],
                ]);
            } else {
                $call->update([
                    'status'        => CallStatusEnum::Failed->value,
                    'status_reason' => $decision['reason'],
                ]);
            }

            // A deferred row returns to pending and is re-dispatched on every tick, so it can loop
            // indefinitely while looking like an untouched "Scheduled" row in the UI.
            Log::warning('[PhoneCallAgent] Outbound call skipped by plan limit', [
                'call_id'  => $call->id,
                'reason'   => $decision['reason'],
                'outcome'  => $call->campaign_id || $call->scheduled_at ? 'deferred to pending (will retry every tick)' : 'failed',
                'user_id'  => $call->user_id,
            ]);

            return;
        }

        Log::debug('[PhoneCallAgent] Placing outbound call', [
            'call_id'  => $call->id,
            'agent_id' => $call->agent_id,
            'provider' => $call->agent->provider->value,
        ]);

        try {
            $service->initiateOutboundCall($call);
            $this->incrementCampaign($call, 'completed_count');
        } catch (Throwable $e) {
            Log::error('[PhoneCallAgent] Outbound call job failed', [
                'call_id'     => $call->id,
                'campaign_id' => $call->campaign_id,
                'error'       => $e->getMessage(),
            ]);

            $this->incrementCampaign($call, 'failed_count');
        }
    }

    /**
     * Runs when the job dies outside handle()'s own try/catch — a worker timeout, a fatal error, a
     * released job that exhausted its attempts. Without this the row keeps the Queued claim it took
     * at the top of handle() and shows as "In Progress" forever with no reason.
     */
    public function failed(?Throwable $e): void
    {
        $call = $this->call->fresh();

        // Only a claimed-but-never-dialled row is ours to fail; anything with a started_at is already
        // live at the provider and will be finalized by the status webhook.
        if (! $call || $call->status !== CallStatusEnum::Queued || $call->started_at) {
            return;
        }

        Log::error('[PhoneCallAgent] Outbound call job died before dialling; marking the row failed', [
            'call_id' => $call->id,
            'error'   => $e?->getMessage(),
        ]);

        $call->update([
            'status'        => CallStatusEnum::Failed->value,
            'status_reason' => $e?->getMessage() ?: __('phone-call-agent::phone-call-agent.outbound_call_never_dialed'),
        ]);
    }

    private function incrementCampaign(ExtPhoneCallAgentCall $call, string $column): void
    {
        if ($call->campaign) {
            $call->campaign->increment($column);
        }
    }
}
