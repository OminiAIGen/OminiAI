<?php

declare(strict_types=1);

namespace App\Extensions\PhoneCallAgent\System\Services\Providers;

use App\Models\SettingTwo;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ElevenLabsPhoneService
{
    private string $endpoint = 'https://api.elevenlabs.io/';

    private ?string $apiKey;

    public function __construct()
    {
        $settings = SettingTwo::getCache();
        $this->apiKey = $settings?->elevenlabs_api_key;
    }

    public function listPhoneNumbers(): array
    {
        $res = Http::withHeaders($this->getHeaders())
            ->get($this->endpoint . 'v1/convai/phone-numbers');

        if ($res->successful()) {
            return $res->json('phone_numbers', []);
        }

        return [];
    }

    /**
     * Import a phone number into the ElevenLabs workspace. The payload's `provider`
     * discriminator selects the source: `twilio`, `sip_trunk`, or `exotel`.
     *
     * @param  array<string, mixed>  $payload
     *
     * @return array{phone_number_id?: string, detail?: mixed}
     */
    public function importPhoneNumber(array $payload): array
    {
        $res = Http::withHeaders($this->getHeaders())
            ->post($this->endpoint . 'v1/convai/phone-numbers', $payload);

        return $res->json() ?? [];
    }

    /**
     * Assign an agent to an existing phone number.
     *
     * @return array<string, mixed>
     */
    public function assignAgent(string $phoneNumberId, string $agentId): array
    {
        $res = Http::withHeaders($this->getHeaders())
            ->patch($this->endpoint . 'v1/convai/phone-numbers/' . $phoneNumberId, [
                'agent_id' => $agentId,
            ]);

        return $res->json() ?? [];
    }

    public function removePhoneNumber(string $phoneNumberId): void
    {
        Http::withHeaders($this->getHeaders())
            ->delete($this->endpoint . 'v1/convai/phone-numbers/' . $phoneNumberId);
    }

    /**
     * Place an outbound call. Uses the Twilio outbound endpoint for Twilio-imported numbers and
     * the SIP-trunk endpoint otherwise. ElevenLabs runs the whole call; the result arrives via the
     * post-call webhook, matched on the returned conversation_id.
     *
     * @return array{conversation_id?: string, callSid?: string, success?: bool, message?: string, detail?: mixed}
     */
    public function outboundCall(string $agentId, string $phoneNumberId, string $toNumber, bool $viaSipTrunk = false, ?string $promptOverride = null): array
    {
        $path = $viaSipTrunk
            ? 'v1/convai/sip-trunk/outbound-call'
            : 'v1/convai/twilio/outbound-call';

        $payload = [
            'agent_id'              => $agentId,
            'agent_phone_number_id' => $phoneNumberId,
            'to_number'             => $toNumber,
        ];

        // A follow-up call ships a fully composed replacement prompt (the agent's instructions plus its
        // call purpose) built by PhoneCallAgentService, which also opts the agent into prompt overrides
        // — ElevenLabs silently discards this block otherwise.
        if ($promptOverride !== null && $promptOverride !== '') {
            $payload['conversation_initiation_client_data'] = [
                'conversation_config_override' => [
                    'agent' => [
                        'prompt' => ['prompt' => $promptOverride],
                    ],
                ],
            ];
        }

        $res = Http::withHeaders($this->getHeaders())
            ->post($this->endpoint . $path, $payload);

        // A non-2xx here otherwise degrades into the generic "failed to place the call" string, hiding
        // the actual reason (bad key, unverified number, agent not found).
        if ($res->failed()) {
            Log::error('[PhoneCallAgent] ElevenLabs outbound call rejected', [
                'path'     => $path,
                'status'   => $res->status(),
                'agent_id' => $agentId,
                'to'       => $toNumber,
                'response' => Str::limit($res->body(), 500),
            ]);
        }

        return $res->json() ?? [];
    }

    private function getHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'xi-api-key'   => $this->apiKey,
        ];
    }
}
