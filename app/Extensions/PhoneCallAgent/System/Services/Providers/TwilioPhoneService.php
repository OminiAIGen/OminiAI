<?php

declare(strict_types=1);

namespace App\Extensions\PhoneCallAgent\System\Services\Providers;

use App\Extensions\PhoneCallAgent\System\Models\ExtPhoneCallAgent;
use App\Models\SettingTwo;
use Exception;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;
use Twilio\TwiML\VoiceResponse;

class TwilioPhoneService
{
    private ?string $accountSid;

    private ?string $authToken;

    public function __construct()
    {
        $settings = SettingTwo::getCache();
        $this->accountSid = $settings?->twilio_account_sid ?? config('twilio.account_sid');
        $this->authToken = $settings?->twilio_auth_token ?? config('twilio.auth_token');
    }

    /**
     * Build a Twilio client using the supplied per-agent credentials, falling back to the global ones.
     */
    public function clientFor(?string $sid = null, ?string $token = null): Client
    {
        return new Client($sid ?: $this->accountSid, $token ?: $this->authToken);
    }

    public function client(): Client
    {
        return $this->clientFor();
    }

    /**
     * Point an existing Twilio number (in the given account) at our inbound + status webhooks.
     *
     * @return string the Twilio IncomingPhoneNumber SID
     *
     * @throws RuntimeException when the credentials are invalid or the number is not in the account
     */
    public function configureInboundNumber(string $sid, string $token, string $phoneNumber, string $voiceUrl, string $statusUrl): string
    {
        try {
            $client = $this->clientFor($sid, $token);

            $numbers = $client->incomingPhoneNumbers->read(['phoneNumber' => $phoneNumber], 1);

            if (empty($numbers)) {
                throw new RuntimeException(__('phone-call-agent::phone-call-agent.twilio_number_not_found'));
            }

            $numberSid = $numbers[0]->sid;

            $client->incomingPhoneNumbers($numberSid)->update([
                'voiceUrl'             => $voiceUrl,
                'voiceMethod'          => 'POST',
                'statusCallback'       => $statusUrl,
                'statusCallbackMethod' => 'POST',
            ]);

            return $numberSid;
        } catch (TwilioException $e) {
            throw new RuntimeException(__('phone-call-agent::phone-call-agent.twilio_credentials_invalid'), 0, $e);
        }
    }

    /**
     * Best-effort: clear our webhook off the number so it stops routing to us.
     */
    public function clearInboundNumber(string $sid, string $token, ?string $numberSid): void
    {
        if (! $numberSid) {
            return;
        }

        try {
            $this->clientFor($sid, $token)
                ->incomingPhoneNumbers($numberSid)
                ->update(['voiceUrl' => '', 'statusCallback' => '']);
        } catch (TwilioException $e) {
            // Number may already be gone or credentials rotated; releasing locally is what matters.
        }
    }

    /**
     * Map a stored Say/Polly-format voice_id (e.g. "Polly.Joanna-Neural", "Google.en-US-Standard-B")
     * to ConversationRelay tts attributes. ConversationRelay rejects the Say format outright, which
     * silences TTS (no greeting, no replies).
     *
     * @return array{ttsProvider?: string, voice?: string}
     */
    public static function conversationRelayVoice(?string $voiceId): array
    {
        if (! $voiceId) {
            return [];
        }

        if (str_starts_with($voiceId, 'Polly.')) {
            return ['ttsProvider' => 'Amazon', 'voice' => substr($voiceId, 6)];
        }

        if (str_starts_with($voiceId, 'Google.')) {
            return ['ttsProvider' => 'Google', 'voice' => substr($voiceId, 7)];
        }

        return ['voice' => $voiceId];
    }

    /**
     * Resolve the public host Twilio should reach for the ConversationRelay WebSocket.
     * Uses the admin-configured host, falling back to the app URL host. Never includes a scheme.
     */
    public static function publicHost(): string
    {
        $host = (string) (setting('phone_call_agent_public_host') ?: parse_url((string) config('app.url'), PHP_URL_HOST));

        return preg_replace('#^https?://#', '', $host) ?? $host;
    }

    /**
     * Build the ConversationRelay TwiML that connects a Twilio call to our relay WebSocket.
     * Shared by the inbound webhook, the outbound path, and the relay test command.
     */
    public static function buildConversationRelayTwiml(ExtPhoneCallAgent $agent, string $host, bool $withGreeting = true): string
    {
        $host = preg_replace('#^https?://#', '', $host) ?? $host;
        $wsUrl = 'wss://' . $host . '/api/phone-call-agent/ws/twilio/' . $agent->uuid;

        $attrs = array_merge(
            ['url' => $wsUrl],
            self::conversationRelayVoice($agent->voice_id ?: 'Polly.Joanna-Neural'),
        );

        if ($withGreeting && $agent->welcome_message) {
            $attrs['welcomeGreeting'] = $agent->welcome_message;
        }

        if ($agent->language && $agent->language !== 'auto') {
            $attrs['language'] = $agent->language;
        }

        $response = new VoiceResponse;
        $response->connect()->conversationRelay($attrs);

        return (string) $response;
    }

    public function buildInboundTwiml(ExtPhoneCallAgent $agent): string
    {
        $agentId = $agent->agent_id;

        return <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Response>
    <Connect>
        <Stream url="wss://api.elevenlabs.io/v1/convai/conversation?agent_id={$agentId}" />
    </Connect>
</Response>
XML;
    }

    /**
     * Place an outbound call that connects to our ConversationRelay WebSocket, wiring the status
     * callback so call duration/status is finalized when the call ends.
     *
     * @return string the Twilio Call SID
     */
    public function placeOutboundCall(string $sid, string $token, string $to, string $from, string $twiml, string $statusUrl): string
    {
        try {
            $call = $this->clientFor($sid, $token)
                ->calls
                ->create($to, $from, [
                    'twiml'                => $twiml,
                    'statusCallback'       => $statusUrl,
                    'statusCallbackMethod' => 'POST',
                    'statusCallbackEvent'  => ['completed'],
                ]);
        } catch (Throwable $e) {
            Log::error('[PhoneCallAgent] Twilio outbound call rejected', [
                'to'    => $to,
                'from'  => $from,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }

        return $call->sid;
    }

    /**
     * @return array{status?: string, duration?: int}
     */
    public function fetchCallDetails(string $callSid, ?string $sid = null, ?string $token = null): array
    {
        try {
            $call = $this->clientFor($sid, $token)->calls($callSid)->fetch();

            return [
                'status'   => $call->status,
                'duration' => (int) $call->duration,
            ];
        } catch (Exception $e) {
            return [];
        }
    }
}
