<?php

declare(strict_types=1);

namespace App\Extensions\PhoneCallAgent\System\Services;

use App\Extensions\PhoneCallAgent\System\Booking\BookingPromptComposer;
use App\Extensions\PhoneCallAgent\System\Booking\BookingToolDefinitions;
use App\Extensions\PhoneCallAgent\System\Enums\CallSourceEnum;
use App\Extensions\PhoneCallAgent\System\Enums\CallStatusEnum;
use App\Extensions\PhoneCallAgent\System\Enums\DirectionEnum;
use App\Extensions\PhoneCallAgent\System\Models\ExtPhoneCallAgent;
use App\Extensions\PhoneCallAgent\System\Models\ExtPhoneCallAgentCall;
use App\Extensions\PhoneCallAgent\System\Models\ExtPhoneCallAgentTrain;
use App\Extensions\PhoneCallAgent\System\Services\Providers\ElevenLabsPhoneService;
use App\Extensions\PhoneCallAgent\System\Services\Providers\TwilioPhoneService;
use App\Services\Ai\ElevenLabsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Throwable;

class PhoneCallAgentService
{
    /**
     * Mirrors the webhook group registered in PhoneCallAgentServiceProvider::registerRoutes().
     */
    private const TWILIO_INBOUND_PATH = 'api/phone-call-agent/webhook/twilio/inbound';

    private const TWILIO_STATUS_PATH = 'api/phone-call-agent/webhook/twilio/status';

    public function __construct(
        protected ElevenLabsService $elevenLabs,
        protected ElevenLabsPhoneService $phoneService,
        protected TwilioPhoneService $twilioPhone,
        protected BookingToolDefinitions $bookingTools,
        protected BookingPromptComposer $bookingPrompt
    ) {}

    public function query(): Builder
    {
        return ExtPhoneCallAgent::query();
    }

    /**
     * Provision the remote agent from the raw form input. The booking guidance is not composed here
     * because booking settings belong to the saved model — PhoneCallAgentController::store() calls
     * updateAgent() immediately afterwards, which appends it.
     */
    public function createAgent(array $args): JsonResponse
    {
        $conversationConfig = [
            'agent' => [
                'first_message' => $args['welcome_message'] ?? '',
                'prompt'        => [
                    'prompt' => $args['instructions'] ?? '',
                ],
            ],
            'tts' => [
                'voice_id' => $args['voice_id'] ?? ElevenLabsService::DEFAULT_ELEVENLABS_VOICE_ID,
                'model_id' => ElevenLabsService::DEFAULT_ELEVENLABS_MODEL_FOR_ENGLISH,
            ],
        ];

        if (isset($args['language']) && $args['language'] !== null && $args['language'] !== 'auto') {
            $conversationConfig['agent']['language'] = $args['language'];
            if ($args['language'] !== 'en') {
                $conversationConfig['tts']['model_id'] = ElevenLabsService::DEFAULT_ELEVENLABS_MODEL;
            }
        }

        return $this->elevenLabs->createAgent(
            conversation_config: $conversationConfig,
            platform_settings: $this->platformSettingsWithOverrides(),
            name: $args['title'],
        );
    }

    /**
     * Push the local agent to ElevenLabs. Returns false when the remote update was rejected, so the
     * caller can warn instead of reporting a save that never reached the provider.
     */
    public function updateAgent(ExtPhoneCallAgent $agent): bool
    {
        if ($agent->provider->value === 'twilio') {
            return true;
        }

        if (! $agent->agent_id) {
            return false;
        }

        // Fetch current config to preserve fields we don't manage (tools, timezone, etc.)
        $current = $this->elevenLabs->getAgent($agent->agent_id);
        $currentData = $current->getData(true)['resData'] ?? [];
        $currentConfig = $currentData['conversation_config'] ?? [];
        $currentOverrides = $currentData['platform_settings']['overrides'] ?? [];

        $promptConfig = array_merge(
            $currentConfig['agent']['prompt'] ?? [],
            ['prompt' => $this->elevenLabsInstructions($agent)]
        );

        // Create/delete the standalone booking tools. Capture the prior ids first so we can drop
        // them from the agent config regardless of whether booking was just toggled off.
        $previousToolIds = $agent->booking_tool_ids ?? [];
        $toolIds = $this->syncBookingTools($agent);

        // Tools are managed as standalone entities referenced by tool_ids. Never echo inline `tools`
        // back — ElevenLabs rejects its own serialized inline-tool payload (dynamic_variable
        // placeholders, etc.). This also migrates legacy agents off inline booking tools.
        unset($promptConfig['tools']);

        // Rebuild tool_ids: drop our previously-managed ids, then re-add current ones if booking is on.
        $promptConfig['tool_ids'] = array_values(array_filter(
            $promptConfig['tool_ids'] ?? [],
            fn ($id) => ! in_array($id, $previousToolIds, true)
        ));

        if ($agent->booking_enabled && ! empty($toolIds)) {
            $promptConfig['tool_ids'] = array_values(array_unique(array_merge($promptConfig['tool_ids'], $toolIds)));
        }

        if (empty($promptConfig['tool_ids'])) {
            unset($promptConfig['tool_ids']);
        }

        $agentConfig = [
            'first_message' => $agent->welcome_message ?? '',
            'prompt'        => $promptConfig,
        ];

        if ($agent->language && $agent->language !== 'auto') {
            $agentConfig['language'] = $agent->language;
        }

        $ttsConfig = [
            'voice_id' => $agent->voice_id,
            'model_id' => ($agent->language && $agent->language !== 'auto' && $agent->language !== 'en')
                ? ElevenLabsService::DEFAULT_ELEVENLABS_MODEL
                : ElevenLabsService::DEFAULT_ELEVENLABS_MODEL_FOR_ENGLISH,
        ];

        $conversationConfig = [
            'agent' => $agentConfig,
            'tts'   => $ttsConfig,
        ];

        return $this->updateAgentWithKnowledgebase($agent, $conversationConfig, $currentOverrides);
    }

    public function deleteAgent(ExtPhoneCallAgent $agent): void
    {
        if ($agent->provider->value === 'twilio') {
            return;
        }

        $this->deleteBookingTools($agent->booking_tool_ids ?? []);

        if ($agent->agent_id) {
            $this->elevenLabs->deleteAgent($agent->agent_id);
        }
    }

    /**
     * Reconcile the agent's standalone ElevenLabs booking tools with its booking_enabled state.
     * Creates the tools (storing their ids) when booking is on, force-deletes them when off.
     *
     * @return array<int, string> the agent's current booking tool ids
     */
    public function syncBookingTools(ExtPhoneCallAgent $agent): array
    {
        if ($agent->provider->value === 'twilio') {
            return [];
        }

        $existing = $agent->booking_tool_ids ?? [];

        if ($agent->booking_enabled) {
            if (! empty($existing)) {
                return $existing;
            }

            $toolIds = [];
            foreach ($this->bookingTools->toolConfigsForElevenLabs($agent) as $config) {
                $res = $this->elevenLabs->createTool($config);
                $data = $res->getData();

                if (($data->status ?? '') === 'success' && isset($data->resData->id)) {
                    $toolIds[] = $data->resData->id;
                }
            }

            $agent->forceFill(['booking_tool_ids' => $toolIds ?: null])->save();

            return $toolIds;
        }

        if (! empty($existing)) {
            $this->deleteBookingTools($existing);
            $agent->forceFill(['booking_tool_ids' => null])->save();
        }

        return [];
    }

    /**
     * @param  array<int, string>  $toolIds
     */
    private function deleteBookingTools(array $toolIds): void
    {
        foreach ($toolIds as $toolId) {
            $this->elevenLabs->deleteTool($toolId, force: true);
        }
    }

    public function getConversationSignedUrl(string $agentId): JsonResponse
    {
        return $this->elevenLabs->getSignedUrl($agentId);
    }

    /**
     * Phone numbers in the shared ElevenLabs workspace that belong to one of this user's agents.
     *
     * @return array<int, array<string, mixed>>
     */
    public function listAgentPhoneNumbers(int $userId): array
    {
        $ownAgentIds = ExtPhoneCallAgent::query()
            ->where('user_id', $userId)
            ->whereNotNull('agent_id')
            ->pluck('agent_id')
            ->all();

        if (empty($ownAgentIds)) {
            return [];
        }

        return array_values(array_filter(
            $this->phoneService->listPhoneNumbers(),
            fn (array $number): bool => in_array($number['assigned_agent']['agent_id'] ?? null, $ownAgentIds, true)
        ));
    }

    /**
     * Import a number into ElevenLabs (from Twilio, a SIP trunk, or Exotel), assign it to the
     * agent, and persist it locally. The payload's `provider` discriminator selects the source.
     *
     * @param  array<string, mixed>  $payload
     *
     * @return array<string, mixed>
     */
    public function importAndAttachPhoneNumber(ExtPhoneCallAgent $agent, array $payload): array
    {
        $res = $this->phoneService->importPhoneNumber($payload);

        $phoneNumberId = $res['phone_number_id'] ?? null;

        if (! $phoneNumberId) {
            throw new RuntimeException($res['detail']['message'] ?? __('phone-call-agent::phone-call-agent.phone_number_import_failed'));
        }

        $this->phoneService->assignAgent($phoneNumberId, (string) $agent->agent_id);

        $attributes = [
            'phone_number'    => $payload['phone_number'],
            'phone_number_id' => $phoneNumberId,
        ];

        // Twilio credentials are reusable, so keep them on the agent; SIP/Exotel secrets live in ElevenLabs.
        if (($payload['provider'] ?? null) === 'twilio') {
            $attributes['twilio_account_sid'] = $payload['sid'] ?? null;
            $attributes['twilio_auth_token'] = $payload['token'] ?? null;
        }

        $agent->forceFill($attributes)->save();

        return $res;
    }

    /**
     * Reassign an already-imported number to this agent, moving it off any of the user's other agents.
     */
    public function assignExistingPhoneNumber(ExtPhoneCallAgent $agent, string $phoneNumberId, string $phoneNumber): void
    {
        $this->phoneService->assignAgent($phoneNumberId, (string) $agent->agent_id);

        ExtPhoneCallAgent::query()
            ->where('user_id', $agent->user_id)
            ->where('phone_number_id', $phoneNumberId)
            ->whereKeyNot($agent->getKey())
            ->update(['phone_number' => null, 'phone_number_id' => null]);

        $agent->forceFill([
            'phone_number'    => $phoneNumber,
            'phone_number_id' => $phoneNumberId,
        ])->save();
    }

    public function releasePhoneNumber(ExtPhoneCallAgent $agent): void
    {
        if ($agent->phone_number_id) {
            $this->phoneService->removePhoneNumber($agent->phone_number_id);
        }

        $agent->forceFill([
            'phone_number'    => null,
            'phone_number_id' => null,
        ])->save();
    }

    /**
     * Point a user-owned Twilio number at our webhooks and persist the agent's Twilio credentials.
     */
    public function configureTwilioNumber(ExtPhoneCallAgent $agent, string $phoneNumber, string $sid, string $token): void
    {
        $numberSid = $this->twilioPhone->configureInboundNumber(
            $sid,
            $token,
            $phoneNumber,
            $this->twilioWebhookUrl('api.phone-call-agent.webhook.twilio.inbound', self::TWILIO_INBOUND_PATH),
            $this->twilioWebhookUrl('api.phone-call-agent.webhook.twilio.status', self::TWILIO_STATUS_PATH),
        );

        $agent->forceFill([
            'phone_number'       => $phoneNumber,
            'phone_number_id'    => $numberSid,
            'twilio_account_sid' => $sid,
            'twilio_auth_token'  => $token,
        ])->save();
    }

    public function releaseTwilioNumber(ExtPhoneCallAgent $agent): void
    {
        if ($agent->twilio_account_sid && $agent->twilio_auth_token) {
            $this->twilioPhone->clearInboundNumber($agent->twilio_account_sid, $agent->twilio_auth_token, $agent->phone_number_id);
        }

        $agent->forceFill([
            'phone_number'    => null,
            'phone_number_id' => null,
        ])->save();
    }

    /**
     * Create a pending outbound call row (the unit of work for single, campaign, and scheduled calls).
     */
    public function createOutboundCall(
        ExtPhoneCallAgent $agent,
        string $toNumber,
        ?int $campaignId = null,
        ?Carbon $scheduledAt = null,
        CallSourceEnum $source = CallSourceEnum::Manual,
        ?string $followUpInstruction = null,
        ?int $bookingId = null
    ): ExtPhoneCallAgentCall {
        $call = ExtPhoneCallAgentCall::create([
            'uuid'                  => Str::uuid()->toString(),
            'agent_id'              => $agent->id,
            'campaign_id'           => $campaignId,
            'booking_id'            => $bookingId,
            'user_id'               => $agent->user_id,
            'caller_number'         => $agent->phone_number,
            'called_number'         => $toNumber,
            'direction'             => DirectionEnum::Outbound,
            'source'                => $source,
            'status'                => CallStatusEnum::Pending,
            'scheduled_at'          => $scheduledAt,
            'follow_up_instruction' => $followUpInstruction,
        ]);

        Log::debug('[PhoneCallAgent] Outbound call row created', [
            'call_id'       => $call->id,
            'agent_id'      => $agent->id,
            'campaign_id'   => $campaignId,
            'source'        => $source->value,
            'to'            => $toNumber,
            'scheduled_at'  => $scheduledAt?->toDateTimeString(),
            'dispatched_by' => $scheduledAt ? 'scheduler' : 'caller',
        ]);

        return $call;
    }

    /**
     * Dial a pending outbound call via its agent's provider. Stores the provider call id and marks
     * the row Queued; the relay/webhook layer finalizes duration and status. Marks the row Failed
     * and rethrows on provider error.
     */
    public function initiateOutboundCall(ExtPhoneCallAgentCall $call): void
    {
        $agent = $call->agent;
        $startedAt = microtime(true);

        try {
            $callSid = $agent->provider->value === 'twilio'
                ? $this->placeTwilioOutboundCall($agent, (string) $call->called_number, $call->follow_up_instruction)
                : $this->placeElevenLabsOutboundCall($agent, (string) $call->called_number, $call->follow_up_instruction);

            $call->update([
                'call_sid'      => $callSid,
                'status'        => CallStatusEnum::Queued,
                'status_reason' => null,
                'started_at'    => now(),
            ]);

            Log::info('[PhoneCallAgent] Outbound call placed', [
                'call_id'    => $call->id,
                'agent_id'   => $agent->id,
                'provider'   => $agent->provider->value,
                'to'         => $call->called_number,
                'call_sid'   => $callSid,
                'elapsed_ms' => (int) ((microtime(true) - $startedAt) * 1000),
            ]);
        } catch (Throwable $e) {
            Log::error('[PhoneCallAgent] Outbound call failed', [
                'call_id'  => $call->id,
                'agent_id' => $agent->id,
                'provider' => $agent->provider->value,
                'to'       => $call->called_number,
                'error'    => $e->getMessage(),
            ]);

            $call->update([
                'status'        => CallStatusEnum::Failed,
                'status_reason' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    private function placeTwilioOutboundCall(ExtPhoneCallAgent $agent, string $toNumber, ?string $followUpInstruction = null): string
    {
        if (! $agent->twilio_account_sid || ! $agent->twilio_auth_token || ! $agent->phone_number) {
            throw new RuntimeException(__('phone-call-agent::phone-call-agent.outbound_agent_no_number'));
        }

        /*
         * A follow-up call must open with its Call Purpose (spoken by the LLM once the relay
         * connects), so the agent's static welcomeGreeting is suppressed — otherwise Twilio
         * speaks a generic inbound-style greeting before the LLM ever runs.
         */
        $twiml = TwilioPhoneService::buildConversationRelayTwiml(
            $agent,
            TwilioPhoneService::publicHost(),
            withGreeting: $followUpInstruction === null || $followUpInstruction === '',
        );

        return $this->twilioPhone->placeOutboundCall(
            $agent->twilio_account_sid,
            $agent->twilio_auth_token,
            $toNumber,
            $agent->phone_number,
            $twiml,
            $this->twilioWebhookUrl('api.phone-call-agent.webhook.twilio.status', self::TWILIO_STATUS_PATH),
        );
    }

    /**
     * The extension registers its routes from the provider's boot(), so a stale long-lived worker or
     * a compiled route cache can leave the dialling process without them — which used to abort the
     * call with "Route [...] not defined". Twilio only needs a publicly reachable URL, so fall back
     * to the same host the ConversationRelay stream already uses.
     */
    private function twilioWebhookUrl(string $routeName, string $path): string
    {
        try {
            return route($routeName);
        } catch (RouteNotFoundException $e) {
            $url = 'https://' . TwilioPhoneService::publicHost() . '/' . $path;

            Log::warning('[PhoneCallAgent] Webhook route not registered in this process; using the public host', [
                'route' => $routeName,
                'url'   => $url,
            ]);

            return $url;
        }
    }

    private function placeElevenLabsOutboundCall(ExtPhoneCallAgent $agent, string $toNumber, ?string $followUpInstruction = null): string
    {
        if (! $agent->agent_id || ! $agent->phone_number_id) {
            throw new RuntimeException(__('phone-call-agent::phone-call-agent.outbound_agent_no_number'));
        }

        $promptOverride = $this->composeFollowUpPrompt($agent, $followUpInstruction);

        if ($promptOverride !== null) {
            $this->enablePromptOverrides($agent);
        }

        // ElevenLabs outbound runs over the underlying carrier: Twilio for Twilio-imported numbers,
        // the SIP-trunk endpoint otherwise.
        $viaSipTrunk = empty($agent->twilio_account_sid);

        $res = $this->phoneService->outboundCall($agent->agent_id, $agent->phone_number_id, $toNumber, $viaSipTrunk, $promptOverride);

        $conversationId = $res['conversation_id'] ?? $res['callSid'] ?? null;

        if (! $conversationId || ($res['success'] ?? true) === false) {
            throw new RuntimeException(
                $res['message'] ?? ($res['detail']['message'] ?? __('phone-call-agent::phone-call-agent.outbound_provider_failed'))
            );
        }

        return $conversationId;
    }

    /**
     * Build the per-call prompt override for a follow-up call: the agent's own instructions plus a
     * "# Call Purpose" section, mirroring the Twilio ConversationRelay path. Returns null when the
     * call carries no follow-up instruction, so ordinary outbound calls send no override at all.
     */
    private function composeFollowUpPrompt(ExtPhoneCallAgent $agent, ?string $followUpInstruction): ?string
    {
        $instruction = trim((string) $followUpInstruction);

        if ($instruction === '') {
            return null;
        }

        return $this->elevenLabsInstructions($agent) . "\n\n# Call Purpose\n\n" . $instruction;
    }

    /**
     * The prompt an ElevenLabs agent runs on: the user's own instructions plus the booking guidance.
     * The booking block only ever exists remotely — `$agent->instructions` in the database stays the
     * raw user text, so repeated saves cannot make it accumulate.
     */
    private function elevenLabsInstructions(ExtPhoneCallAgent $agent): string
    {
        return $this->bookingPrompt->appendForElevenLabs(trim((string) $agent->instructions), $agent);
    }

    /**
     * ElevenLabs ignores a per-call conversation_config_override unless the agent explicitly opts in.
     *
     * Only the `overrides` subtree is ever sent. The GET agent response also carries response-only
     * fields (auth.shareable_token, safety.is_blocked_ivc, ban, widget output) that the PATCH input
     * model rejects — echoing the whole body 4xx's the entire update, including the prompt and
     * tool_ids it carries. Same reason `prompt.tools` is stripped in updateAgent().
     *
     * @param  array<string, mixed>  $currentOverrides  value of platform_settings.overrides, if fetched
     *
     * @return array<string, mixed>
     */
    private function platformSettingsWithOverrides(array $currentOverrides = []): array
    {
        return [
            'overrides' => deepMerge($currentOverrides, [
                'conversation_config_override' => [
                    'agent' => [
                        'prompt' => ['prompt' => true],
                    ],
                ],
            ]),
        ];
    }

    /**
     * Lazily opt an existing agent into prompt overrides before a follow-up call. Agents provisioned
     * before overrides were sent would otherwise silently ignore the call-purpose prompt. Unlike
     * updateAgent() this does not read the agent's current overrides first, to keep the outbound
     * dial path to a single request.
     */
    private function enablePromptOverrides(ExtPhoneCallAgent $agent): void
    {
        try {
            $this->elevenLabs->updateAgent(
                agent_id: (string) $agent->agent_id,
                platform_settings: $this->platformSettingsWithOverrides(),
            );
        } catch (Throwable $e) {
            Log::warning('[PhoneCallAgent] Could not enable prompt overrides', [
                'agent_id' => $agent->id,
                'error'    => $e->getMessage(),
            ]);
        }
    }

    public function getVoices(string $provider = 'elevenlabs'): array
    {
        if ($provider === 'twilio') {
            return $this->getTwilioVoices();
        }

        $res = $this->elevenLabs->getListOfVoices(page_size: 100);
        if ($res->getData()->status === 'success') {
            return $res->getData()->resData->voices;
        }

        return [];
    }

    private function getTwilioVoices(): array
    {
        return [
            ['voice_id' => 'Polly.Joanna-Neural', 'name' => 'Joanna (US English, Female)'],
            ['voice_id' => 'Polly.Matthew-Neural', 'name' => 'Matthew (US English, Male)'],
            ['voice_id' => 'Polly.Salli-Neural', 'name' => 'Salli (US English, Female)'],
            ['voice_id' => 'Polly.Amy-Neural', 'name' => 'Amy (British English, Female)'],
            ['voice_id' => 'Polly.Brian-Neural', 'name' => 'Brian (British English, Male)'],
            ['voice_id' => 'Polly.Emma-Neural', 'name' => 'Emma (British English, Female)'],
            ['voice_id' => 'Polly.Lucia-Neural', 'name' => 'Lucia (Spanish, Female)'],
            ['voice_id' => 'Polly.Lupe-Neural', 'name' => 'Lupe (Spanish US, Female)'],
            ['voice_id' => 'Polly.Lea-Neural', 'name' => 'Léa (French, Female)'],
            ['voice_id' => 'Polly.Vicki-Neural', 'name' => 'Vicki (German, Female)'],
            ['voice_id' => 'Polly.Bianca-Neural', 'name' => 'Bianca (Italian, Female)'],
            ['voice_id' => 'Google.en-US-Standard-B', 'name' => 'Google English US (Male)'],
            ['voice_id' => 'Google.en-US-Standard-C', 'name' => 'Google English US (Female)'],
            ['voice_id' => 'Google.en-GB-Standard-A', 'name' => 'Google English GB (Female)'],
            ['voice_id' => 'Google.fr-FR-Standard-A', 'name' => 'Google French (Female)'],
            ['voice_id' => 'Google.de-DE-Standard-A', 'name' => 'Google German (Female)'],
            ['voice_id' => 'Google.es-ES-Standard-A', 'name' => 'Google Spanish (Female)'],
        ];
    }

    public function addKnowledgebase(string $type, mixed $content, ?string $name = null): JsonResponse
    {
        return match ($type) {
            'text' => $this->elevenLabs->createKnowledgebaseDocFromText(text: $content, name: $name),
            'url'  => $this->elevenLabs->createKnowledgebaseDocFromUrl(url: $content, name: $name),
            'file' => $this->elevenLabs->createKnowledgebaseDocFromFile(file: $content, name: $name),
        };
    }

    public function deleteKnowledgebase(string $docId): void
    {
        $this->elevenLabs->deleteKnowledgebaseDocument($docId);
    }

    /**
     * @param  array<string, mixed>  $conversationConfig
     * @param  array<string, mixed>  $platformOverrides  the agent's current remote override opt-ins, preserved on merge
     */
    public function updateAgentWithKnowledgebase(ExtPhoneCallAgent $agent, array $conversationConfig = [], array $platformOverrides = []): bool
    {
        $trains = ExtPhoneCallAgentTrain::query()
            ->whereNotNull('trained_at')
            ->whereNotNull('doc_id')
            ->where('agent_id', $agent->getKey())
            ->get();

        $trainedKnowledges = $trains->map(fn ($train) => [
            'id'   => $train->doc_id,
            'name' => $train->name,
            'type' => $train->type,
        ])->all();

        if (! empty($trainedKnowledges)) {
            $conversationConfig['agent']['prompt']['knowledge_base'] = $trainedKnowledges;
        }

        if (! $agent->agent_id || empty($conversationConfig)) {
            return false;
        }

        $res = $this->elevenLabs->updateAgent(
            agent_id: $agent->agent_id,
            conversation_config: $conversationConfig,
            platform_settings: $this->platformSettingsWithOverrides($platformOverrides),
            name: $agent->title,
        );

        if (($res->getData()->status ?? '') === 'success') {
            return true;
        }

        // This PATCH also carries prompt.tool_ids and the booking prompt. If platform_settings is
        // what ElevenLabs rejected, dropping it still gets the agent's tools and prompt attached
        // rather than leaving the whole update silently unapplied.
        Log::error('[PhoneCallAgent] ElevenLabs agent update rejected; retrying without platform_settings', [
            'agent_id'  => $agent->id,
            'remote_id' => $agent->agent_id,
        ]);

        $retry = $this->elevenLabs->updateAgent(
            agent_id: $agent->agent_id,
            conversation_config: $conversationConfig,
            name: $agent->title,
        );

        if (($retry->getData()->status ?? '') === 'success') {
            return true;
        }

        Log::error('[PhoneCallAgent] ElevenLabs agent update failed', [
            'agent_id'  => $agent->id,
            'remote_id' => $agent->agent_id,
        ]);

        return false;
    }
}
