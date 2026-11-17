<?php

declare(strict_types=1);

namespace App\Extensions\PhoneCallAgent\System\WebSocket;

use App\Domains\Entity\EntityManager;
use App\Extensions\PhoneCallAgent\System\Booking\BookingPromptComposer;
use App\Extensions\PhoneCallAgent\System\Booking\BookingProviderResolver;
use App\Extensions\PhoneCallAgent\System\Booking\BookingToolDefinitions;
use App\Extensions\PhoneCallAgent\System\Booking\BookingToolHandler;
use App\Extensions\PhoneCallAgent\System\Enums\CallStatusEnum;
use App\Extensions\PhoneCallAgent\System\Enums\DirectionEnum;
use App\Extensions\PhoneCallAgent\System\Enums\RoleEnum;
use App\Extensions\PhoneCallAgent\System\Models\ExtPhoneCallAgent;
use App\Extensions\PhoneCallAgent\System\Models\ExtPhoneCallAgentCall;
use App\Extensions\PhoneCallAgent\System\Models\ExtPhoneCallAgentTrain;
use App\Extensions\PhoneCallAgent\System\Models\ExtPhoneCallAgentTranscript;
use App\Extensions\PhoneCallAgent\System\Services\FollowUpService;
use App\Extensions\PhoneCallAgent\System\Services\TwilioLlmService;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use SplObjectStorage;

class ConversationRelayHandler implements MessageComponentInterface
{
    protected SplObjectStorage $clients;

    /** @var array<int, array{agent: ExtPhoneCallAgent, call: ExtPhoneCallAgentCall|null, systemPrompt: string, history: array}> */
    protected array $state = [];

    public function __construct()
    {
        $this->clients = new SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn): void
    {
        $path = $conn->httpRequest->getUri()->getPath();
        $uuid = basename($path);

        Log::info('[Twilio WS] Connection opened', ['path' => $path, 'uuid' => $uuid]);

        $agent = ExtPhoneCallAgent::query()
            ->where('uuid', $uuid)
            ->where('provider', 'twilio')
            ->where('active', true)
            ->first();

        if (! $agent) {
            Log::warning('[Twilio WS] No active agent for uuid', ['uuid' => $uuid]);
            $conn->close();

            return;
        }

        Log::info('[Twilio WS] Agent loaded', ['agent_id' => $agent->id, 'model' => $agent->ai_model]);

        $this->clients->attach($conn);
        $this->state[spl_object_id($conn)] = [
            'agent'        => $agent,
            'call'         => null,
            'systemPrompt' => $this->buildSystemPrompt($agent),
            'history'      => [],
        ];
    }

    public function onMessage(ConnectionInterface $from, $msg): void
    {
        $payload = json_decode((string) $msg, true);

        if (! is_array($payload)) {
            return;
        }

        // ConversationRelay frames carry their discriminator in `type`; keep `event` as a fallback.
        $event = $payload['type'] ?? $payload['event'] ?? null;
        $id = spl_object_id($from);

        Log::info('[Twilio WS] Message', ['type' => $event]);

        if (! isset($this->state[$id])) {
            return;
        }

        match ($event) {
            'setup', 'connected'         => $this->handleConnected($from, $payload, $id),
            'prompt'                     => $this->handlePrompt($from, $payload, $id),
            'interrupt', 'dtmf', 'error' => null,
            default                      => null,
        };
    }

    public function onClose(ConnectionInterface $conn): void
    {
        $id = spl_object_id($conn);
        Log::info('[Twilio WS] Connection closed');
        $this->handleDisconnect($id);
        $this->clients->detach($conn);
    }

    public function onError(ConnectionInterface $conn, Exception $e): void
    {
        Log::error('[Twilio WS] Error', ['message' => $e->getMessage()]);
        $conn->close();
    }

    private function handleConnected(ConnectionInterface $conn, array $payload, int $id): void
    {
        $callSid = $payload['callSid'] ?? null;

        if (! $callSid) {
            return;
        }

        // Both `setup` and `connected` route here; a second frame must not re-append the
        // Call Purpose or speak the opening twice.
        if ($this->state[$id]['call'] !== null) {
            return;
        }

        $agent = $this->state[$id]['agent'];

        $call = ExtPhoneCallAgentCall::query()
            ->where('call_sid', $callSid)
            ->first();

        if (! $call && ! empty($payload['to'])) {
            // initiateOutboundCall() persists call_sid only after the dial returns, so Twilio can open
            // this socket first. Adopt the row we just dialled instead of creating a duplicate that
            // would drop its follow-up instruction.
            $call = ExtPhoneCallAgentCall::query()
                ->where('agent_id', $agent->id)
                ->where('direction', DirectionEnum::Outbound->value)
                ->whereIn('status', [CallStatusEnum::Pending->value, CallStatusEnum::Queued->value])
                ->where('called_number', $payload['to'])
                ->where('created_at', '>=', now()->subMinutes(10))
                ->latest('id')
                ->first();

            $call?->update(['call_sid' => $callSid]);
        }

        if (! $call) {
            // Outbound/test calls have no inbound webhook to pre-create the record; create it here
            // so the conversation still lands in call history.
            $call = ExtPhoneCallAgentCall::create([
                'uuid'          => Str::uuid()->toString(),
                'agent_id'      => $agent->id,
                'user_id'       => $agent->user_id,
                'call_sid'      => $callSid,
                'caller_number' => $payload['from'] ?? null,
                'called_number' => $payload['to'] ?? null,
                'direction'     => DirectionEnum::Outbound,
                'status'        => CallStatusEnum::Answered,
                'started_at'    => now(),
            ]);
            Log::info('[Twilio WS] Call record created', ['callSid' => $callSid, 'call_id' => $call->id]);
        } else {
            Log::info('[Twilio WS] Call record linked', ['callSid' => $callSid, 'call_id' => $call->id]);
        }

        $this->state[$id]['call'] = $call;

        // Follow-up calls carry a per-call purpose (reminder, survey, …) that the agent must act on.
        // The callee did not initiate this call, so the agent speaks first: the static TwiML
        // welcomeGreeting is suppressed for these calls and the LLM opens with the Call Purpose.
        if ($call->follow_up_instruction) {
            $this->state[$id]['systemPrompt'] .= "\n\n# Call Purpose\n\n" . $call->follow_up_instruction;
            $this->speakOpening($conn, $id);
        }
    }

    /**
     * Generate and speak the LLM's opening line for an agent-initiated call.
     */
    private function speakOpening(ConnectionInterface $conn, int $id): void
    {
        $ctx = &$this->state[$id];

        $ctx['history'][] = [
            'role'    => 'user',
            'content' => '(The outbound call has just been answered. Greet the callee briefly and carry out the Call Purpose. Do not mention these bracketed instructions.)',
        ];

        $result = $this->callLlm($ctx);
        $spoken = $this->stripToolNarration($result['spoken']);

        Log::info('[Twilio WS] Follow-up opening', ['response' => $spoken]);

        $ctx['history'][] = ['role' => 'assistant', 'content' => $result['memory']];

        if ($ctx['call']) {
            ExtPhoneCallAgentTranscript::create([
                'call_id' => $ctx['call']->id,
                'role'    => RoleEnum::agent->value,
                'message' => $spoken,
            ]);
        }

        $conn->send(json_encode([
            'type'  => 'text',
            'token' => $spoken,
            'last'  => true,
        ]));
    }

    private function handlePrompt(ConnectionInterface $conn, array $payload, int $id): void
    {
        $voicePrompt = $payload['voicePrompt'] ?? '';

        if ($voicePrompt === '') {
            return;
        }

        $ctx = &$this->state[$id];
        $agent = $ctx['agent'];
        $call = $ctx['call'];

        Log::info('[Twilio WS] User prompt', ['prompt' => $voicePrompt]);

        $ctx['history'][] = ['role' => 'user', 'content' => $voicePrompt];

        if ($call) {
            ExtPhoneCallAgentTranscript::create([
                'call_id' => $call->id,
                'role'    => RoleEnum::user->value,
                'message' => $voicePrompt,
            ]);
        }

        $result = $this->callLlm($ctx);
        $spoken = $this->stripToolNarration($result['spoken']);

        Log::info('[Twilio WS] LLM response', ['response' => $spoken]);

        // History keeps the richer `memory` (includes internal tool results) so the model
        // recalls slots/UIDs on later turns; the caller only hears `spoken`.
        $ctx['history'][] = ['role' => 'assistant', 'content' => $result['memory']];

        if ($call) {
            ExtPhoneCallAgentTranscript::create([
                'call_id' => $call->id,
                'role'    => RoleEnum::agent->value,
                'message' => $spoken,
            ]);
        }

        $conn->send(json_encode([
            'type'  => 'text',
            'token' => $spoken,
            'last'  => true,
        ]));
    }

    private function handleDisconnect(int $id): void
    {
        if (! isset($this->state[$id])) {
            return;
        }

        $call = $this->state[$id]['call'];

        if ($call && $call->status !== CallStatusEnum::Completed) {
            $call->update([
                'status'   => CallStatusEnum::Completed,
                'ended_at' => now(),
                'duration' => $call->started_at ? max(0, now()->diffInSeconds($call->started_at)) : ($call->duration ?? 0),
            ]);

            if ($call->direction === DirectionEnum::Inbound) {
                app(FollowUpService::class)->scheduleAfterCall($call->refresh());
            }
        }

        unset($this->state[$id]);
    }

    private function buildSystemPrompt(ExtPhoneCallAgent $agent): string
    {
        $instructions = $agent->instructions ?? '';

        $trains = ExtPhoneCallAgentTrain::query()
            ->where('agent_id', $agent->getKey())
            ->whereNotNull('trained_at')
            ->get();

        if (! $trains->isEmpty()) {
            $kb = $trains
                ->map(fn ($t) => trim($t->content ?? $t->text ?? ''))
                ->filter()
                ->map(fn ($text, $i) => isset($trains[$i]->name) && $trains[$i]->name
                    ? "## {$trains[$i]->name}\n{$text}"
                    : $text
                )
                ->implode("\n\n---\n\n");

            $instructions .= "\n\n# Knowledge Base\n\n" . $kb;
        }

        return app(BookingPromptComposer::class)->appendForRelay($instructions, $agent);
    }

    /**
     * Run one LLM turn.
     *
     * @return array{spoken: string, memory: string} `spoken` is sent to the caller; `memory` is
     *                                               stored in history (may embed internal tool results)
     */
    private function callLlm(array $ctx): array
    {
        $agent = $ctx['agent'];
        $user = $agent->user;

        try {
            // Use the platform's default chat model and bill the call against the owner's plan/credits.
            $driver = app(EntityManager::class)->driverForUser($user);
            $model = $driver->enum()->value;

            if (! $driver->isUnlimitedCredit() && ! $driver->hasCreditBalance()) {
                Log::warning('[Twilio WS] No credit balance', ['user_id' => $agent->user_id]);

                return $this->reply(__('phone-call-agent::phone-call-agent.no_credits_voice'));
            }

            $llm = app(TwilioLlmService::class);

            if ($this->bookingReady($agent)) {
                $provider = app(BookingProviderResolver::class)->resolve($agent);
                // The customer sits on opposite ends of the row depending on direction: they are
                // the caller on inbound calls but the called party on agent-initiated ones.
                $customerNumber = $ctx['call']
                    ? ($ctx['call']->direction === DirectionEnum::Inbound ? $ctx['call']->caller_number : $ctx['call']->called_number)
                    : null;
                $handler = new BookingToolHandler($provider, $agent, $customerNumber);
                $definitions = new BookingToolDefinitions;
                $tools = $this->toolsForEngine($model, $definitions);

                $result = $llm->completeWithTools(
                    model: $model,
                    systemPrompt: $ctx['systemPrompt'],
                    messages: $ctx['history'],
                    tools: $tools,
                    toolHandler: fn (string $name, array $input) => $handler->handle($name, $input),
                );

                $spoken = $result['text'];
                $memory = $result['toolSummary'] !== ''
                    ? $result['toolSummary'] . "\n\n" . $spoken
                    : $spoken;
            } else {
                $spoken = $llm->complete(
                    model: $model,
                    systemPrompt: $ctx['systemPrompt'],
                    messages: $ctx['history'],
                );
                $memory = $spoken;
            }

            $this->deductCredit($user, $spoken);

            return ['spoken' => $spoken, 'memory' => $memory];
        } catch (Exception $e) {
            Log::error('[Twilio WS] LLM error', ['error' => $e->getMessage()]);

            return $this->reply("I'm sorry, I encountered an error. Please try again.");
        }
    }

    /** @return array{spoken: string, memory: string} */
    private function reply(string $text): array
    {
        return ['spoken' => $text, 'memory' => $text];
    }

    /**
     * Strip raw JSON tool-call narration from the spoken text.
     *
     * Weaker models sometimes write the tool arguments (and even a fabricated result) as a raw
     * JSON object inside their reply instead of emitting a structured tool call. Left untouched it
     * would be read aloud by TTS and stored in the transcript. We remove flat JSON object blobs and
     * tidy the surrounding whitespace, falling back to the original text if nothing readable remains.
     */
    private function stripToolNarration(string $text): string
    {
        if (! str_contains($text, '{')) {
            return $text;
        }

        $cleaned = preg_replace('/\{[^{}]*\}/', '', $text) ?? $text;
        $cleaned = preg_replace('/[ \t]{2,}/', ' ', $cleaned) ?? $cleaned;
        $cleaned = trim(preg_replace('/\n{3,}/', "\n\n", $cleaned) ?? $cleaned);

        return $cleaned !== '' ? $cleaned : $text;
    }

    /**
     * Booking is usable only when enabled AND a provider + event type are configured. Both the
     * system-prompt capability blurb and the tool wiring must agree, or the model would be told it
     * can book while having no tools — producing fabricated confirmations.
     */
    private function bookingReady(ExtPhoneCallAgent $agent): bool
    {
        return app(BookingPromptComposer::class)->isReady($agent);
    }

    /**
     * Bill the generated response against the owner's plan/shared credits via the Entity system.
     */
    private function deductCredit(User $user, string $responseText): void
    {
        if (trim($responseText) === '') {
            return;
        }

        try {
            app(EntityManager::class)->driverForUser($user)
                ->input($responseText)
                ->calculateCredit()
                ->decreaseCredit();
        } catch (Exception $e) {
            Log::error('[Twilio WS] Credit deduction failed', ['user_id' => $user->id, 'error' => $e->getMessage()]);
        }
    }

    private function toolsForEngine(string $model, BookingToolDefinitions $definitions): array
    {
        return match (true) {
            str_starts_with($model, 'claude')  => $definitions->forAnthropic(),
            str_starts_with($model, 'gemini')  => $definitions->forGemini(),
            default                            => $definitions->forOpenAi(),
        };
    }
}
