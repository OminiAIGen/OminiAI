<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Http\Controllers;

use App\Extensions\VideoAgent\System\Jobs\RefreshVideoAgentStatusJob;
use App\Extensions\VideoAgent\System\Models\VideoAgentSession;
use App\Extensions\VideoAgent\System\Models\VideoAgentVideo;
use App\Extensions\VideoAgent\System\Services\VideoAgentService;
use App\Extensions\VideoAgent\System\Services\VideoAgentWebhookVerifier;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Receiver for HeyGen webhook deliveries. HeyGen only tells us that something
 * changed, so the handler always re-reads the authoritative state from the API
 * rather than trusting the payload.
 */
class VideoAgentWebhookController extends Controller
{
    public function __construct(
        private readonly VideoAgentWebhookVerifier $verifier,
        private readonly VideoAgentService $service,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        if (! $this->verifier->verify($request)) {
            return response()->json(['message' => 'Invalid signature'], 401);
        }

        $event = $this->verifier->claim($request);

        if ($event === null) {
            // Already handled: HeyGen retries deliveries, we must stay idempotent.
            return response()->json(['duplicate' => true]);
        }

        $this->dispatchRefresh($request);

        $event->forceFill(['processed_at' => now()])->save();

        return response()->json(['received' => true]);
    }

    private function dispatchRefresh(Request $request): void
    {
        $session = $this->resolveSession($request);

        if ($session !== null) {
            RefreshVideoAgentStatusJob::dispatch($session->getKey());

            return;
        }

        $videoId = $request->input('event_data.video_id', $request->input('video_id'));

        if (blank($videoId)) {
            return;
        }

        $video = VideoAgentVideo::query()->where('heygen_id', $videoId)->first();

        if ($video !== null) {
            $this->service->syncVideo($video);
        }
    }

    private function resolveSession(Request $request): ?VideoAgentSession
    {
        $callbackId = $request->input('event_data.callback_id', $request->input('callback_id'));

        if (filled($callbackId)) {
            $session = VideoAgentSession::query()->where('uuid', $callbackId)->first();

            if ($session !== null) {
                return $session;
            }
        }

        $sessionId = $request->input('event_data.session_id', $request->input('session_id'));

        if (blank($sessionId)) {
            return null;
        }

        return VideoAgentSession::query()->where('heygen_session_id', $sessionId)->first();
    }
}
