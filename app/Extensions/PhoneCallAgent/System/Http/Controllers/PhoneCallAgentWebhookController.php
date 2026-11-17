<?php

declare(strict_types=1);

namespace App\Extensions\PhoneCallAgent\System\Http\Controllers;

use App\Extensions\PhoneCallAgent\System\Booking\BookingProviderResolver;
use App\Extensions\PhoneCallAgent\System\Booking\BookingToolHandler;
use App\Extensions\PhoneCallAgent\System\Enums\CallStatusEnum;
use App\Extensions\PhoneCallAgent\System\Enums\DirectionEnum;
use App\Extensions\PhoneCallAgent\System\Models\ExtPhoneCallAgent;
use App\Extensions\PhoneCallAgent\System\Models\ExtPhoneCallAgentCall;
use App\Extensions\PhoneCallAgent\System\Models\ExtPhoneCallAgentTranscript;
use App\Extensions\PhoneCallAgent\System\Services\FollowUpService;
use App\Extensions\PhoneCallAgent\System\Services\PhoneCallAgentLimitService;
use App\Extensions\PhoneCallAgent\System\Services\Providers\TwilioPhoneService;
use App\Helpers\Classes\Helper;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;
use Twilio\Security\RequestValidator;

class PhoneCallAgentWebhookController extends Controller
{
    public function twilioInbound(Request $request): Response
    {
        if (Helper::appIsDemo()) {
            return response($this->buildRejectTwiml('This service is not available in demo mode.'), 200)
                ->header('Content-Type', 'text/xml');
        }

        $calledNumber = $request->input('To');
        $callerNumber = $request->input('From');
        $callSid = $request->input('CallSid');

        Log::info('[Twilio] Inbound call', [
            'to'      => $calledNumber,
            'from'    => $callerNumber,
            'callSid' => $callSid,
        ]);

        // Primary: explicit per-agent number mapping. Fallback: legacy call-history scan.
        $agent = ExtPhoneCallAgent::query()
            ->where('active', true)
            ->where('provider', 'twilio')
            ->where('phone_number', $calledNumber)
            ->first();

        if (! $agent) {
            $agent = ExtPhoneCallAgent::query()
                ->where('active', true)
                ->get()
                ->first(function ($agent) use ($calledNumber) {
                    $phoneNumbers = $agent->calls()
                        ->whereNotNull('called_number')
                        ->pluck('called_number')
                        ->unique();

                    return $phoneNumbers->contains($calledNumber);
                });
        }

        if (! $agent) {
            Log::warning('[Twilio] No agent found for number', ['to' => $calledNumber]);

            return response($this->buildRejectTwiml('This number is not in service.'), 200)
                ->header('Content-Type', 'text/xml');
        }

        if (! $this->verifyTwilioSignature($request, $agent)) {
            Log::warning('[Twilio] Invalid signature', ['to' => $calledNumber]);

            return response($this->buildRejectTwiml('Request could not be verified.'), 403)
                ->header('Content-Type', 'text/xml');
        }

        Log::info('[Twilio] Agent matched', ['agent_id' => $agent->id, 'agent' => $agent->title]);

        // Plan limit enforcement
        $user = $agent->user;

        if (! (new PhoneCallAgentLimitService($user))->canReceiveInboundCall()) {
            return response($this->buildRejectTwiml('Service is not available on your plan.'), 200)
                ->header('Content-Type', 'text/xml');
        }

        ExtPhoneCallAgentCall::create([
            'uuid'          => Str::uuid()->toString(),
            'agent_id'      => $agent->id,
            'user_id'       => $user->id,
            'call_sid'      => $callSid,
            'caller_number' => $callerNumber,
            'called_number' => $calledNumber,
            'direction'     => DirectionEnum::Inbound,
            'status'        => CallStatusEnum::Ringing,
            'started_at'    => now(),
        ]);

        if ($agent->provider->value === 'twilio') {
            $twiml = $this->buildConversationRelayTwiml($agent, $request);
            Log::info('[Twilio] Returning ConversationRelay TwiML', ['twiml' => $twiml]);
        } else {
            $agentId = $agent->agent_id;
            $twiml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Response>
    <Connect>
        <Stream url="wss://api.elevenlabs.io/v1/convai/conversation?agent_id={$agentId}" />
    </Connect>
</Response>
XML;
        }

        return response($twiml, 200)->header('Content-Type', 'text/xml');
    }

    public function twilioStatus(Request $request): Response
    {
        $callSid = $request->input('CallSid');
        $callStatus = $request->input('CallStatus');

        Log::info('[Twilio] Status webhook', ['callSid' => $callSid, 'status' => $callStatus]);
        $duration = (int) $request->input('CallDuration', 0);

        $call = ExtPhoneCallAgentCall::query()
            ->where('call_sid', $callSid)
            ->first();

        if ($call && $call->agent && ! $this->verifyTwilioSignature($request, $call->agent)) {
            Log::warning('[Twilio] Invalid status signature', ['callSid' => $callSid]);

            return response('', 403);
        }

        if ($call) {
            $status = match ($callStatus) {
                'in-progress', 'ringing' => CallStatusEnum::Answered,
                'completed'              => CallStatusEnum::Completed,
                'failed', 'busy'         => CallStatusEnum::Failed,
                'no-answer'              => CallStatusEnum::Missed,
                default                  => CallStatusEnum::Completed,
            };

            $call->update([
                'status'   => $status,
                'duration' => $duration,
                'ended_at' => $callStatus === 'completed' ? now() : null,
            ]);
        }

        return response('', 204);
    }

    public function elevenLabsInbound(Request $request): Response
    {
        if (Helper::appIsDemo()) {
            return response('', 204);
        }

        if (! $this->verifyElevenLabsSignature($request)) {
            return response('Unauthorized', 401);
        }

        $payload = $request->all();
        $type = $payload['type'] ?? null;

        // ElevenLabs post-call webhook: single payload after call ends
        if ($type === 'post_call_transcription') {
            $data = $payload['data'] ?? [];
            $conversationId = $data['conversation_id'] ?? null;
            $agentId = $data['agent_id'] ?? null;
            $transcript = $data['transcript'] ?? [];
            $duration = (int) ($data['metadata']['call_duration_secs'] ?? $data['duration_seconds'] ?? 0);

            if (! $conversationId || ! $agentId) {
                return response('', 204);
            }

            $agent = ExtPhoneCallAgent::query()
                ->where('agent_id', $agentId)
                ->first();

            if (! $agent) {
                return response('', 204);
            }

            $call = ExtPhoneCallAgentCall::query()
                ->where('call_sid', $conversationId)
                ->first();

            $justCompleted = false;
            $direction = $this->resolveElevenLabsDirection($data);
            $numbers = $this->resolveElevenLabsNumbers($data, $direction);

            if (! $call) {
                $call = ExtPhoneCallAgentCall::create([
                    'uuid'          => Str::uuid()->toString(),
                    'agent_id'      => $agent->id,
                    'user_id'       => $agent->user_id,
                    'call_sid'      => $conversationId,
                    'caller_number' => $numbers['caller_number'],
                    'called_number' => $numbers['called_number'] ?? $agent->phone_number,
                    'direction'     => $direction,
                    'status'        => CallStatusEnum::Completed,
                    'duration'      => $duration,
                    'started_at'    => now()->subSeconds($duration),
                    'ended_at'      => now(),
                ]);
                $justCompleted = true;
            } else {
                $justCompleted = $call->status !== CallStatusEnum::Completed;

                $attributes = [
                    'status'   => CallStatusEnum::Completed,
                    'duration' => $duration,
                    'ended_at' => now(),
                ];

                // Backfill only what the row is missing. The direction is never rewritten: an outbound
                // row already carries its own numbers, and flipping it to inbound would make the
                // follow-up guard below schedule follow-ups off follow-up calls.
                if (! $call->caller_number && $numbers['caller_number']) {
                    $attributes['caller_number'] = $numbers['caller_number'];
                }

                if (! $call->called_number && $numbers['called_number']) {
                    $attributes['called_number'] = $numbers['called_number'];
                }

                $call->update($attributes);
            }

            if ($justCompleted && $call->direction === DirectionEnum::Inbound) {
                app(FollowUpService::class)->scheduleAfterCall($call->refresh());
            }

            foreach ($transcript as $turn) {
                $message = $turn['message'] ?? '';
                $role = $turn['role'] ?? 'user';

                if ($message) {
                    ExtPhoneCallAgentTranscript::create([
                        'call_id' => $call->id,
                        'role'    => $role,
                        'message' => $message,
                    ]);
                }
            }

            return response('', 204);
        }

        // Legacy / real-time streaming event types (kept for forward-compat)
        $conversationId = $payload['conversation_id'] ?? null;
        $agentId = $payload['agent_id'] ?? null;

        if ($type === 'conversation_initiation_metadata' && $conversationId && $agentId) {
            $agent = ExtPhoneCallAgent::query()
                ->where('agent_id', $agentId)
                ->first();

            if ($agent) {
                ExtPhoneCallAgentCall::create([
                    'uuid'       => Str::uuid()->toString(),
                    'agent_id'   => $agent->id,
                    'user_id'    => $agent->user_id,
                    'call_sid'   => $conversationId,
                    'direction'  => DirectionEnum::Inbound,
                    'status'     => CallStatusEnum::Answered,
                    'started_at' => now(),
                ]);
            }
        }

        if ($type === 'transcript' && $conversationId) {
            $call = ExtPhoneCallAgentCall::query()
                ->where('call_sid', $conversationId)
                ->first();

            if ($call) {
                $role = $payload['role'] ?? 'user';
                $message = $payload['message'] ?? '';

                if ($message) {
                    ExtPhoneCallAgentTranscript::create([
                        'call_id' => $call->id,
                        'role'    => $role,
                        'message' => $message,
                    ]);
                }
            }
        }

        if ($type === 'conversation_ended' && $conversationId) {
            $call = ExtPhoneCallAgentCall::query()
                ->where('call_sid', $conversationId)
                ->first();

            if ($call) {
                $call->update([
                    'status'   => CallStatusEnum::Completed,
                    'duration' => $payload['duration_seconds'] ?? 0,
                    'ended_at' => now(),
                ]);
            }
        }

        return response('', 204);
    }

    public function elevenLabsTool(Request $request, string $agentUuid, string $toolName): JsonResponse
    {
        $agent = ExtPhoneCallAgent::query()
            ->where('uuid', $agentUuid)
            ->where('active', true)
            ->first();

        if (! $agent || ! $agent->booking_enabled) {
            return response()->json(['result' => 'Booking is not configured for this agent.']);
        }

        Log::info('[ElevenLabs Tool] raw request', ['tool' => $toolName, 'body' => $request->all()]);

        $parameters = $request->has('parameters')
            ? $request->input('parameters', [])
            : $request->all();

        try {
            $provider = app(BookingProviderResolver::class)->resolve($agent);
            $handler = new BookingToolHandler($provider, $agent, $this->resolveElevenLabsCaller($request, $parameters));
            $result = $handler->handle($toolName, $parameters);
        } catch (Throwable $e) {
            Log::error('[ElevenLabs Tool] Handler error', ['tool' => $toolName, 'error' => $e->getMessage()]);
            $result = 'Booking action failed: ' . $e->getMessage();
        }

        return response()->json(['result' => $result]);
    }

    /**
     * Resolve the call direction from an ElevenLabs post-call payload, defaulting to inbound for
     * legacy payloads that omit the phone_call metadata block.
     *
     * @param  array<string, mixed>  $data
     */
    private function resolveElevenLabsDirection(array $data): DirectionEnum
    {
        $direction = $data['metadata']['phone_call']['direction']
            ?? $data['conversation_initiation_client_data']['dynamic_variables']['system__call_direction']
            ?? null;

        return is_string($direction) && str_starts_with(strtolower($direction), 'out')
            ? DirectionEnum::Outbound
            : DirectionEnum::Inbound;
    }

    /**
     * Best-effort caller/called numbers for an ElevenLabs post-call payload. `external_number` is the
     * customer side of the call regardless of direction and `agent_number` is our own number; the
     * system dynamic variables cover payload shapes that omit the phone_call block.
     *
     * @param  array<string, mixed>  $data
     *
     * @return array{caller_number: ?string, called_number: ?string}
     */
    private function resolveElevenLabsNumbers(array $data, DirectionEnum $direction): array
    {
        $variables = $data['conversation_initiation_client_data']['dynamic_variables'] ?? [];
        $phoneCall = $data['metadata']['phone_call'] ?? [];

        $external = $phoneCall['external_number']
            ?? $variables['system__caller_id']
            ?? $data['metadata']['from_number']
            ?? null;

        $internal = $phoneCall['agent_number']
            ?? $variables['system__called_number']
            ?? $data['metadata']['to_number']
            ?? null;

        return $direction === DirectionEnum::Outbound
            ? ['caller_number' => $internal, 'called_number' => $external]
            : ['caller_number' => $external, 'called_number' => $internal];
    }

    /**
     * Best-effort caller number for a booking made over ElevenLabs — used as the follow-up callback
     * target when the caller does not dictate a phone number. ElevenLabs passes it as a system dynamic
     * variable; fall back to any phone the tool collected.
     *
     * @param  array<string, mixed>  $parameters
     */
    private function resolveElevenLabsCaller(Request $request, array $parameters): ?string
    {
        return $request->input('system__caller_id')
            ?? $request->input('caller_id')
            ?? ($parameters['phone'] ?? null);
    }

    /**
     * Validate the X-Twilio-Signature using the resolved agent's auth token.
     * Skips when the agent has no per-agent token (legacy / global-creds setups).
     */
    private function verifyTwilioSignature(Request $request, ExtPhoneCallAgent $agent): bool
    {
        $token = $agent->twilio_auth_token;

        if (empty($token)) {
            return true; // verification disabled when no per-agent token is configured
        }

        $signature = $request->header('X-Twilio-Signature', '');

        if (empty($signature)) {
            return false;
        }

        return (new RequestValidator($token))->validate($signature, $request->fullUrl(), $request->post());
    }

    private function verifyElevenLabsSignature(Request $request): bool
    {
        $secret = setting('elevenlabs_webhook_secret');

        if (empty($secret)) {
            return true; // verification disabled if no secret configured
        }

        $header = $request->header('ElevenLabs-Signature', '');
        $body = $request->getContent();

        // header format: t=<timestamp>,v0=<hex_signature>
        preg_match('/t=(\d+)/', $header, $tMatch);
        preg_match('/v0=([a-f0-9]+)/', $header, $vMatch);

        if (empty($tMatch[1]) || empty($vMatch[1])) {
            Log::error('PhoneCallAgent: invalid ElevenLabs signature header', ['header' => $header]);

            return false;
        }

        $timestamp = $tMatch[1];
        $receivedSig = $vMatch[1];

        // ElevenLabs signs: HMAC-SHA256(secret, "{timestamp}.{body}")
        $expectedWithDot = hash_hmac('sha256', "{$timestamp}.{$body}", $secret);
        // Fallback: some versions sign without dot separator
        $expectedNoDot = hash_hmac('sha256', "{$timestamp}{$body}", $secret);

        if (! hash_equals($expectedWithDot, $receivedSig) && ! hash_equals($expectedNoDot, $receivedSig)) {
            Log::error('PhoneCallAgent: ElevenLabs signature mismatch — check webhook secret in admin settings');

            return false;
        }

        return true;
    }

    private function buildConversationRelayTwiml(ExtPhoneCallAgent $agent, Request $request): string
    {
        $host = $request->header('X-Forwarded-Host') ?: $request->getHost();

        return TwilioPhoneService::buildConversationRelayTwiml($agent, $host);
    }

    private function buildRejectTwiml(string $message): string
    {
        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Response>
    <Say>{$message}</Say>
    <Hangup />
</Response>
XML;
    }
}
