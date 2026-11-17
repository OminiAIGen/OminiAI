<?php

declare(strict_types=1);

namespace App\Extensions\PhoneCallAgent\System\Http\Controllers;

use App\Extensions\PhoneCallAgent\System\Enums\CallSourceEnum;
use App\Extensions\PhoneCallAgent\System\Enums\CallStatusEnum;
use App\Extensions\PhoneCallAgent\System\Enums\DirectionEnum;
use App\Extensions\PhoneCallAgent\System\Http\Requests\OutboundCallRequest;
use App\Extensions\PhoneCallAgent\System\Http\Requests\OutboundCampaignRequest;
use App\Extensions\PhoneCallAgent\System\Http\Requests\RescheduleOutboundCallRequest;
use App\Extensions\PhoneCallAgent\System\Http\Requests\UpdateOutboundCampaignRequest;
use App\Extensions\PhoneCallAgent\System\Jobs\PlaceOutboundCallJob;
use App\Extensions\PhoneCallAgent\System\Models\ExtPhoneCallAgent;
use App\Extensions\PhoneCallAgent\System\Models\ExtPhoneCallAgentCall;
use App\Extensions\PhoneCallAgent\System\Models\ExtPhoneCallAgentCampaign;
use App\Extensions\PhoneCallAgent\System\Services\PhoneCallAgentLimitService;
use App\Extensions\PhoneCallAgent\System\Services\PhoneCallAgentService;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PhoneCallAgentOutboundController extends Controller
{
    public function __construct(private readonly PhoneCallAgentService $service) {}

    /**
     * The dedicated outbound calling & campaign management page.
     */
    public function index(): View
    {
        $agents = ExtPhoneCallAgent::query()
            ->where('user_id', Auth::id())
            ->whereNotNull('phone_number')
            ->orderBy('title')
            ->get()
            ->map(fn (ExtPhoneCallAgent $agent): array => [
                'uuid'         => $agent->uuid,
                'title'        => $agent->title,
                'phone_number' => $agent->phone_number,
                'provider'     => $agent->provider->value,
            ]);

        return view('phone-call-agent::home.outbound.index', ['agents' => $agents]);
    }

    /**
     * Paginated list of the user's outbound calls (scheduled, in-progress, and completed).
     */
    public function calls(Request $request): JsonResponse
    {
        $query = ExtPhoneCallAgentCall::query()
            ->where('user_id', Auth::id())
            ->where('direction', DirectionEnum::Outbound->value)
            ->with('agent:id,uuid,title');

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('agent') && $request->agent !== 'all') {
            $query->whereHas('agent', fn ($agentQuery) => $agentQuery->where('uuid', $request->agent));
        }

        if ($request->filled('source') && $request->source !== 'all') {
            $query->where('source', $request->source);
        }

        if ($request->filled('to')) {
            $query->where('called_number', 'like', '%' . trim((string) $request->to) . '%');
        }

        $direction = strtolower((string) $request->input('dir')) === 'asc' ? 'asc' : 'desc';

        match ($request->input('sort')) {
            'when'     => $query->orderByRaw('COALESCE(started_at, scheduled_at, created_at) ' . $direction),
            'status'   => $query->orderBy('status', $direction),
            'duration' => $query->orderBy('duration', $direction),
            default    => $query->orderBy('created_at', $direction),
        };

        $calls = $query->paginate(15)->through(fn (ExtPhoneCallAgentCall $call): array => [
            'uuid'        => $call->uuid,
            'agent_title' => $call->agent?->title,
            'to_number'   => $call->called_number,
            'from_number' => $call->caller_number,
            'status'      => $call->status->value,
            'source'      => $call->source?->value,
            // Carried in the list payload, not just callLogs, so an overdue row can explain itself
            // in the table without opening the modal.
            'status_reason' => $call->status_reason,
            'duration'      => $call->duration,
            'scheduled_at'  => $call->scheduled_at?->toIso8601String(),
            'started_at'    => $call->started_at?->toIso8601String(),
            'created_at'    => $call->created_at?->toIso8601String(),
        ]);

        return response()->json($calls);
    }

    /**
     * Technical log (API metadata + failure reason) for a single outbound call. This is the raw call
     * record — status, provider call id, timestamps, and why it failed — not the conversation transcript.
     */
    public function callLogs(ExtPhoneCallAgentCall $call): JsonResponse
    {
        if ($call->user_id !== Auth::id()) {
            abort(403);
        }

        $call->load('agent:id,uuid,title,provider');

        return response()->json([
            'call' => [
                'uuid'          => $call->uuid,
                'agent_title'   => $call->agent?->title,
                'provider'      => $call->agent?->provider?->value,
                'to_number'     => $call->called_number,
                'from_number'   => $call->caller_number,
                'status'        => $call->status->value,
                'source'        => $call->source?->value,
                'status_reason' => $call->status_reason,
                'call_sid'      => $call->call_sid,
                'duration'      => $call->duration,
                'scheduled_at'  => $call->scheduled_at?->toIso8601String(),
                'started_at'    => $call->started_at?->toIso8601String(),
                'ended_at'      => $call->ended_at?->toIso8601String(),
                'created_at'    => $call->created_at?->toIso8601String(),
            ],
        ]);
    }

    /**
     * Place a single outbound call (immediately, or scheduled for the future).
     */
    public function call(OutboundCallRequest $request): JsonResponse
    {
        $data = $request->validated();
        $agent = ExtPhoneCallAgent::query()->where('uuid', $data['agent_id'])->firstOrFail();

        if ($error = $this->guard($agent)) {
            return $error;
        }

        if (! $agent->phone_number) {
            return response()->json(['message' => __('phone-call-agent::phone-call-agent.outbound_agent_no_number')], 422);
        }

        $decision = (new PhoneCallAgentLimitService($agent->user))->canPlaceOutboundCall();

        if (! $decision['allowed']) {
            return response()->json(['message' => $decision['reason']], 422);
        }

        $scheduledAt = $this->parseScheduledAt($data['scheduled_at'] ?? null);

        $call = $this->service->createOutboundCall($agent, $data['to_number'], null, $scheduledAt, CallSourceEnum::Manual);

        if ($scheduledAt) {
            return response()->json([
                'message' => __('phone-call-agent::phone-call-agent.outbound_call_scheduled'),
                'call'    => ['uuid' => $call->uuid, 'scheduled_at' => $scheduledAt->toIso8601String()],
            ]);
        }

        PlaceOutboundCallJob::dispatch($call);

        return response()->json([
            'message' => __('phone-call-agent::phone-call-agent.outbound_call_queued'),
            'call'    => ['uuid' => $call->uuid],
        ]);
    }

    /**
     * Create a bulk outbound campaign. Recipients are queued as pending calls and dispatched by the
     * scheduler (which honours the per-day cap and spreads a large list across days).
     */
    public function storeCampaign(OutboundCampaignRequest $request): JsonResponse
    {
        $data = $request->validated();
        $agent = ExtPhoneCallAgent::query()->where('uuid', $data['agent_id'])->firstOrFail();

        if ($error = $this->guard($agent)) {
            return $error;
        }

        if (! $agent->phone_number) {
            return response()->json(['message' => __('phone-call-agent::phone-call-agent.outbound_agent_no_number')], 422);
        }

        $decision = (new PhoneCallAgentLimitService($agent->user))->canPlaceOutboundCall();

        if (! $decision['allowed']) {
            return response()->json(['message' => $decision['reason']], 422);
        }

        $numbers = array_values(array_unique($data['numbers']));
        $scheduledAt = $this->parseScheduledAt($data['scheduled_at'] ?? null);

        $campaign = ExtPhoneCallAgentCampaign::create([
            'uuid'             => Str::uuid()->toString(),
            'agent_id'         => $agent->id,
            'user_id'          => $agent->user_id,
            'name'             => $data['name'],
            'status'           => 'pending',
            'scheduled_at'     => $scheduledAt,
            'total_recipients' => count($numbers),
        ]);

        foreach ($numbers as $number) {
            $this->service->createOutboundCall($agent, $number, $campaign->id, $scheduledAt, CallSourceEnum::Campaign);
        }

        return response()->json([
            'message'  => __('phone-call-agent::phone-call-agent.campaign_created'),
            'campaign' => $this->campaignPayload($campaign),
        ]);
    }

    public function campaigns(): JsonResponse
    {
        $campaigns = ExtPhoneCallAgentCampaign::query()
            ->where('user_id', Auth::id())
            ->latest()
            ->get()
            ->map(fn (ExtPhoneCallAgentCampaign $campaign) => $this->campaignPayload($campaign));

        return response()->json(['campaigns' => $campaigns]);
    }

    public function showCampaign(ExtPhoneCallAgentCampaign $campaign): JsonResponse
    {
        if ($campaign->user_id !== Auth::id()) {
            abort(403);
        }

        $campaign->load('agent:id,uuid,title');

        $calls = $campaign->calls()
            ->orderByRaw('COALESCE(started_at, scheduled_at, created_at) desc')
            ->get()
            ->map(fn (ExtPhoneCallAgentCall $call): array => [
                'uuid'          => $call->uuid,
                'to_number'     => $call->called_number,
                'status'        => $call->status->value,
                'status_reason' => $call->status_reason,
                'duration'      => $call->duration,
                'scheduled_at'  => $call->scheduled_at?->toIso8601String(),
                'started_at'    => $call->started_at?->toIso8601String(),
                'created_at'    => $call->created_at?->toIso8601String(),
            ]);

        return response()->json([
            'campaign' => $this->campaignPayload($campaign) + [
                'agent_title' => $campaign->agent?->title,
                'numbers'     => $calls->where('status', CallStatusEnum::Pending->value)->pluck('to_number')->values()->all(),
                'calls'       => $calls->values()->all(),
            ],
        ]);
    }

    /**
     * Edit a campaign that has not finished: rename it, reschedule it, and reconcile its recipient
     * list. Only not-yet-placed (pending) calls are added, moved, or dropped — calls already placed
     * (completed / failed / dialing) are left untouched.
     */
    public function updateCampaign(UpdateOutboundCampaignRequest $request, ExtPhoneCallAgentCampaign $campaign): JsonResponse
    {
        if (Helper::appIsDemo()) {
            return response()->json(['message' => __('phone-call-agent::phone-call-agent.demo_mode_disabled')], 403);
        }

        if ($campaign->user_id !== Auth::id()) {
            abort(403);
        }

        if (in_array($campaign->status, ['completed', 'cancelled'], true)) {
            return response()->json(['message' => __('phone-call-agent::phone-call-agent.campaign_not_editable')], 422);
        }

        $data = $request->validated();
        $numbers = array_values(array_unique($data['numbers']));
        $scheduledAt = $this->parseScheduledAt($data['scheduled_at'] ?? null);

        $pendingQuery = $campaign->calls()->where('status', CallStatusEnum::Pending->value);

        // Numbers already dialed (any non-pending call) stay as-is and must not be re-queued.
        $placedNumbers = $campaign->calls()
            ->where('status', '!=', CallStatusEnum::Pending->value)
            ->pluck('called_number')
            ->all();

        $currentPending = (clone $pendingQuery)->pluck('called_number')->all();

        // Drop pending recipients the user removed.
        (clone $pendingQuery)->whereNotIn('called_number', $numbers)->delete();

        // Queue pending rows for genuinely new numbers.
        $existing = array_merge($currentPending, $placedNumbers);
        foreach (array_diff($numbers, $existing) as $number) {
            $this->service->createOutboundCall($campaign->agent, $number, $campaign->id, $scheduledAt, CallSourceEnum::Campaign);
        }

        // Re-time the recipients that are still pending after the reconcile.
        $campaign->calls()
            ->where('status', CallStatusEnum::Pending->value)
            ->update(['scheduled_at' => $scheduledAt]);

        $campaign->update([
            'name'             => $data['name'],
            'scheduled_at'     => $scheduledAt,
            'total_recipients' => $campaign->calls()->count(),
        ]);

        return response()->json([
            'message'  => __('phone-call-agent::phone-call-agent.campaign_updated'),
            'campaign' => $this->campaignPayload($campaign->refresh()),
        ]);
    }

    /**
     * Cancel a single scheduled (pending, not-yet-placed) outbound call.
     */
    public function cancelCall(ExtPhoneCallAgentCall $call): JsonResponse
    {
        if (Helper::appIsDemo()) {
            return response()->json(['message' => __('phone-call-agent::phone-call-agent.demo_mode_disabled')], 403);
        }

        if ($call->user_id !== Auth::id()) {
            abort(403);
        }

        if ($call->direction !== DirectionEnum::Outbound || $call->status !== CallStatusEnum::Pending) {
            return response()->json(['message' => __('phone-call-agent::phone-call-agent.outbound_only_scheduled_cancel')], 422);
        }

        $call->update(['status' => CallStatusEnum::Cancelled]);

        return response()->json(['message' => __('phone-call-agent::phone-call-agent.outbound_call_cancelled')]);
    }

    /**
     * Change the scheduled time of a single not-yet-placed (pending) outbound call.
     */
    public function rescheduleCall(RescheduleOutboundCallRequest $request, ExtPhoneCallAgentCall $call): JsonResponse
    {
        if (Helper::appIsDemo()) {
            return response()->json(['message' => __('phone-call-agent::phone-call-agent.demo_mode_disabled')], 403);
        }

        if ($call->user_id !== Auth::id()) {
            abort(403);
        }

        if ($call->direction !== DirectionEnum::Outbound || $call->status !== CallStatusEnum::Pending) {
            return response()->json(['message' => __('phone-call-agent::phone-call-agent.outbound_only_scheduled_reschedule')], 422);
        }

        $input = $request->validated()['scheduled_at'];
        $scheduledAt = Carbon::parse($input);

        // The other three write paths log this through parseScheduledAt(); reschedule skips that helper
        // because clearing the schedule is not a valid outcome here, so it logs the same line itself.
        Log::debug('[PhoneCallAgent] Outbound call rescheduled', [
            'call_id'      => $call->id,
            'input'        => $input,
            'parsed'       => $scheduledAt->toDateTimeString(),
            'now'          => now()->toDateTimeString(),
            'app_timezone' => config('app.timezone'),
        ]);

        $call->update(['scheduled_at' => $scheduledAt]);

        return response()->json([
            'message' => __('phone-call-agent::phone-call-agent.outbound_call_rescheduled'),
            'call'    => ['uuid' => $call->uuid, 'scheduled_at' => $call->scheduled_at?->toIso8601String()],
        ]);
    }

    public function cancelCampaign(ExtPhoneCallAgentCampaign $campaign): JsonResponse
    {
        if (Helper::appIsDemo()) {
            return response()->json(['message' => __('phone-call-agent::phone-call-agent.demo_mode_disabled')], 403);
        }

        if ($campaign->user_id !== Auth::id()) {
            abort(403);
        }

        $campaign->calls()->where('status', CallStatusEnum::Pending->value)->update(['status' => CallStatusEnum::Failed->value]);
        $campaign->update(['status' => 'cancelled']);

        return response()->json([
            'message'  => __('phone-call-agent::phone-call-agent.campaign_cancelled'),
            'campaign' => $this->campaignPayload($campaign->refresh()),
        ]);
    }

    private function parseScheduledAt(?string $value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        $date = Carbon::parse($value);

        // A non-future value silently becomes an immediate call, which reads as "my schedule was ignored".
        if (! $date->isFuture()) {
            Log::debug('[PhoneCallAgent] Requested schedule is not in the future; treating as immediate', [
                'input'        => $value,
                'parsed'       => $date->toDateTimeString(),
                'now'          => now()->toDateTimeString(),
                'app_timezone' => config('app.timezone'),
            ]);

            return null;
        }

        return $date;
    }

    /**
     * @return array<string, mixed>
     */
    private function campaignPayload(ExtPhoneCallAgentCampaign $campaign): array
    {
        return [
            'uuid'             => $campaign->uuid,
            'name'             => $campaign->name,
            'status'           => $campaign->status,
            'scheduled_at'     => $campaign->scheduled_at?->toIso8601String(),
            'total_recipients' => $campaign->total_recipients,
            'completed_count'  => $campaign->completed_count,
            'failed_count'     => $campaign->failed_count,
            'created_at'       => $campaign->created_at?->toIso8601String(),
        ];
    }

    private function guard(ExtPhoneCallAgent $agent): ?JsonResponse
    {
        if (Helper::appIsDemo()) {
            return response()->json(['message' => __('phone-call-agent::phone-call-agent.demo_mode_disabled')], 403);
        }

        if ($agent->user_id !== Auth::id()) {
            abort(403);
        }

        return null;
    }
}
