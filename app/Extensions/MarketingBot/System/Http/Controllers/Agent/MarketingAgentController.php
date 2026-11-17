<?php

declare(strict_types=1);

namespace App\Extensions\MarketingBot\System\Http\Controllers\Agent;

use App\Extensions\MarketingBot\System\Enums\CampaignStatus;
use App\Extensions\MarketingBot\System\Enums\CampaignType;
use App\Extensions\MarketingBot\System\Models\MarketingCampaign;
use App\Extensions\MarketingBot\System\Models\MarketingCampaignCopilotMessage;
use App\Extensions\MarketingBot\System\Models\Telegram\TelegramContact;
use App\Extensions\MarketingBot\System\Models\Whatsapp\Contact;
use App\Extensions\MarketingBot\System\Models\Whatsapp\Segment;
use App\Extensions\MarketingBot\System\Models\Whatsapp\WhatsappChannel;
use App\Extensions\MarketingBot\System\Services\Chat\MarketingCopilotService;
use App\Extensions\MarketingBot\System\Services\MarketingBotLimitService;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MarketingAgentController extends Controller
{
    /**
     * Number of copilot turns allowed in demo mode.
     */
    private const DEMO_TURN_LIMIT = 3;

    public function __construct(
        private readonly MarketingCopilotService $copilotService,
    ) {}

    /**
     * Create a draft campaign for the chosen channel and open the agent builder.
     */
    public function start(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'channel' => ['required', 'string', 'in:whatsapp,telegram'],
            'message' => ['nullable', 'string', 'max:4000'],
        ]);

        if (Helper::appIsDemo()) {
            return redirect()->route('dashboard.user.marketing-bot.dashboard')
                ->with(['type' => 'error', 'message' => __('This feature is disabled in demo mode.')]);
        }

        $limitService = new MarketingBotLimitService(Auth::user());

        if (! $limitService->isAgentEnabled()) {
            return redirect()->route('dashboard.user.marketing-bot.dashboard')
                ->with(['type' => 'error', 'message' => __('The AI Marketing Agent is not available on your current plan.')]);
        }

        if (! $limitService->canAccessChannel($data['channel'])) {
            return redirect()->route('dashboard.user.marketing-bot.dashboard')
                ->with(['type' => 'error', 'message' => __('This channel is not enabled on your current plan.')]);
        }

        $campaign = MarketingCampaign::query()->create([
            'user_id' => Auth::id(),
            'type'    => $data['channel'],
            'status'  => CampaignStatus::draft->value,
        ]);

        return redirect()->route('dashboard.user.marketing-bot.agent.build', [
            'campaign'     => $campaign->getKey(),
            'copilot_init' => $data['message'] ?? null,
        ]);
    }

    /**
     * Render the campaign builder in the agent layout for a draft.
     */
    public function build(MarketingCampaign $campaign): View
    {
        $this->authorize('edit', $campaign);

        $channel = $campaign->type instanceof CampaignType ? $campaign->type->value : (string) $campaign->type;

        if ($channel === CampaignType::telegram->value) {
            return view('marketing-bot::telegram-campaign.create', [
                'title'           => trans('New Telegram Campaign'),
                'action'          => route('dashboard.user.marketing-bot.telegram-campaign.update', $campaign->getKey()),
                'method'          => 'PUT',
                'item'            => $campaign,
                'contacts'        => TelegramContact::query()->where('user_id', Auth::id())->get(),
                'selectedContact' => $campaign->getAttribute('contacts') ?: [],
            ]);
        }

        return view('marketing-bot::whatsapp-campaign.create', [
            'title'           => trans('New WhatsApp Campaign'),
            'action'          => route('dashboard.user.marketing-bot.whatsapp-campaign.update', $campaign->getKey()),
            'method'          => 'PUT',
            'item'            => $campaign,
            'contacts'        => Contact::my()->get(),
            'segments'        => Segment::my()->get(),
            'selectedContact' => $campaign->getAttribute('contacts') ?: [],
            'selectedSegment' => $campaign->getAttribute('segments') ?: [],
            'whatsapp'        => WhatsappChannel::query()->where('user_id', Auth::id())->first(),
        ]);
    }

    /**
     * Stream the copilot response as Server-Sent Events.
     */
    public function chat(Request $request, MarketingCampaign $campaign): StreamedResponse
    {
        $this->authorize('edit', $campaign);

        $request->validate([
            'message' => ['required', 'string', 'max:4000'],
            'state'   => ['nullable', 'array'],
        ]);

        $limitService = new MarketingBotLimitService(Auth::user());

        abort_unless($limitService->isAgentEnabled(), 403, __('The AI Marketing Agent is not available on your current plan.'));

        $userMessage = trim((string) $request->input('message'));
        $state = (array) $request->input('state', []);

        if (Helper::appIsDemo()) {
            $turns = MarketingCampaignCopilotMessage::query()
                ->where('campaign_id', $campaign->id)
                ->where('role', 'user')
                ->count();

            if ($turns >= self::DEMO_TURN_LIMIT) {
                return $this->demoLimitStream();
            }
        }

        $this->copilotService->saveMessage($campaign, 'user', $userMessage);

        $history = $this->copilotService->loadHistory($campaign, turns: 20);
        $history = array_slice($history, 0, max(0, count($history) - 1));

        return response()->stream(function () use ($campaign, $state, $history, $userMessage): void {
            set_time_limit(120);

            ini_set('output_buffering', 'off');
            ini_set('zlib.output_compression', '0');
            @ob_implicit_flush(true);
            while (ob_get_level() > 0) {
                @ob_end_flush();
            }

            $buffer = [
                'text'                    => '',
                'tool_calls'              => [],
                'thinking_total_duration' => 0,
                'thinking_started'        => null,
                'thinking_has_data'       => false,
            ];

            $this->copilotService->streamChat(
                campaign: $campaign,
                state: $state,
                history: $history,
                userMessage: $userMessage,
                emit: function (array $event) use (&$buffer): void {
                    if ($event['type'] === 'text_delta') {
                        $buffer['text'] .= $event['payload']['text'] ?? '';
                    } elseif ($event['type'] === 'tool_call_end') {
                        $buffer['tool_calls'][] = $event['payload'];
                    } elseif ($event['type'] === 'thinking_start') {
                        $buffer['thinking_started'] = microtime(true);
                        $buffer['thinking_has_data'] = true;
                    } elseif ($event['type'] === 'thinking_end' && $buffer['thinking_started'] !== null) {
                        $buffer['thinking_total_duration'] += (int) round((microtime(true) - $buffer['thinking_started']) * 1000);
                        $buffer['thinking_started'] = null;
                    }

                    echo 'data: ' . json_encode($event) . "\n\n";

                    @ob_flush();
                    @flush();
                },
            );

            $metadata = $buffer['thinking_has_data']
                ? ['thinking' => ['duration_ms' => $buffer['thinking_total_duration']]]
                : null;

            $this->copilotService->saveMessage(
                $campaign,
                'assistant',
                $buffer['text'],
                empty($buffer['tool_calls']) ? null : $buffer['tool_calls'],
                $metadata,
            );
        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    /**
     * Return the persisted copilot conversation for a campaign.
     */
    public function history(MarketingCampaign $campaign): JsonResponse
    {
        $this->authorize('edit', $campaign);

        $messages = MarketingCampaignCopilotMessage::query()
            ->where('campaign_id', $campaign->id)
            ->orderBy('created_at')
            ->get(['id', 'role', 'content', 'tool_calls', 'metadata'])
            ->map(fn (MarketingCampaignCopilotMessage $m) => [
                'id'        => $m->id,
                'role'      => $m->role,
                'content'   => $m->content ?? '',
                'toolCalls' => $m->tool_calls ?? [],
                'metadata'  => $m->metadata ?? null,
            ]);

        return response()->json(['data' => $messages]);
    }

    /**
     * Resolve the live recipient count for the current audience selection.
     */
    public function recipients(Request $request, MarketingCampaign $campaign): JsonResponse
    {
        $this->authorize('edit', $campaign);

        $request->validate([
            'contacts'   => ['nullable', 'array'],
            'segments'   => ['nullable', 'array'],
        ]);

        $campaign->setAttribute('contacts', array_map('intval', (array) $request->input('contacts', [])));
        $campaign->setAttribute('segments', array_map('intval', (array) $request->input('segments', [])));

        return response()->json(['count' => $this->copilotService->resolveRecipients($campaign)]);
    }

    /**
     * Persist the current builder state as a draft without sending.
     */
    public function saveDraft(Request $request, MarketingCampaign $campaign): JsonResponse
    {
        $this->authorize('update', $campaign);

        if (Helper::appIsDemo()) {
            return response()->json(['status' => 'error', 'message' => __('This feature is disabled in demo mode.')], 422);
        }

        $data = $request->validate([
            'name'         => ['nullable', 'string', 'max:255'],
            'content'      => ['nullable', 'string'],
            'contacts'     => ['nullable', 'array'],
            'segments'     => ['nullable', 'array'],
            'scheduled_at' => ['nullable', 'date'],
            'image'        => ['nullable', 'string'],
            'ai_reply'     => ['nullable', 'boolean'],
            'instruction'  => ['nullable', 'string'],
        ]);

        $campaign->fill(array_merge($data, ['status' => CampaignStatus::draft->value]));
        $campaign->save();

        return response()->json(['status' => 'success', 'message' => __('Draft saved')]);
    }

    private function demoLimitStream(): StreamedResponse
    {
        return response()->stream(function (): void {
            $message = __('You have reached the demo conversation limit. Please try the full version to continue.');
            echo 'data: ' . json_encode(['type' => 'text_delta', 'payload' => ['text' => $message]]) . "\n\n";
            echo 'data: ' . json_encode(['type' => 'done', 'payload' => []]) . "\n\n";
            @ob_flush();
            @flush();
        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}
