<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Services;

use App\Extensions\VideoAgent\System\Models\VideoAgentWebhookEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * HeyGen signs every delivery with an HMAC-SHA256 of the raw body. Anyone who
 * learns the endpoint URL could otherwise forge events, so verification is
 * mandatory, replays inside the timestamp window are rejected, and each
 * `Heygen-Event-Id` is processed exactly once.
 */
class VideoAgentWebhookVerifier
{
    public const SIGNATURE_HEADER = 'Heygen-Signature';

    public const TIMESTAMP_HEADER = 'Heygen-Timestamp';

    public const EVENT_ID_HEADER = 'Heygen-Event-Id';

    public const TOLERANCE_SECONDS = 300;

    public function secret(): ?string
    {
        $secret = setting('video_agent_webhook_secret');

        return filled($secret) ? (string) $secret : null;
    }

    public function verify(Request $request): bool
    {
        $secret = $this->secret();

        if (blank($secret)) {
            Log::warning('Video Agent webhook received but no signing secret is configured.');

            return false;
        }

        $signature = (string) $request->header(self::SIGNATURE_HEADER);

        if (blank($signature)) {
            return false;
        }

        if (! $this->timestampIsFresh($request)) {
            return false;
        }

        $expected = hash_hmac('sha256', $request->getContent(), $secret);

        return hash_equals($expected, $signature);
    }

    public function timestampIsFresh(Request $request): bool
    {
        $timestamp = $request->header(self::TIMESTAMP_HEADER);

        if (blank($timestamp) || ! is_numeric($timestamp)) {
            return false;
        }

        return abs(now()->getTimestamp() - (int) $timestamp) <= self::TOLERANCE_SECONDS;
    }

    /**
     * Claim the delivery. Returns null when this event id was already seen,
     * which makes the caller a no-op for HeyGen retries.
     */
    public function claim(Request $request): ?VideoAgentWebhookEvent
    {
        $eventId = (string) $request->header(self::EVENT_ID_HEADER);

        if (blank($eventId)) {
            $eventId = 'anonymous-' . hash('sha256', $request->getContent());
        }

        try {
            return VideoAgentWebhookEvent::query()->create([
                'heygen_event_id' => $eventId,
                'event_type'      => $request->input('event_type'),
                'payload'         => $request->all(),
            ]);
        } catch (Throwable) {
            // Unique index hit: HeyGen redelivered an event we already handled.
            return null;
        }
    }
}
