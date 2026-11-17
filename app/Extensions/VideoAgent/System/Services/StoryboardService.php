<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Services;

use App\Extensions\VideoAgent\System\Models\VideoAgentResource;
use App\Extensions\VideoAgent\System\Models\VideoAgentSession;
use App\Extensions\VideoAgent\System\Services\HeyGen\HeyGenClient;

/**
 * Fetches and caches the scene-by-scene plan the agent produces at the
 * `reviewing` checkpoint, so the storyboard panel can re-render on every poll
 * without hitting HeyGen again.
 */
class StoryboardService
{
    public function __construct(private readonly HeyGenClient $client) {}

    /**
     * @param  array<string, mixed>  $session Raw `GET /v3/video-agents/{id}` payload
     */
    public function syncFromSessionPayload(VideoAgentSession $model, array $session): ?VideoAgentResource
    {
        $resourceId = $this->resourceIdFrom($session);

        if (blank($resourceId)) {
            return null;
        }

        $existing = $model->resources()->where('heygen_resource_id', $resourceId)->first();

        if ($existing !== null && filled($existing->payload)) {
            return $existing;
        }

        return $this->fetch($model, $resourceId);
    }

    public function fetch(VideoAgentSession $model, string $resourceId): ?VideoAgentResource
    {
        $response = $this->client->getVideoAgentResource(
            (string) $model->heygen_session_id,
            $resourceId,
        );

        if (isset($response['error'])) {
            return null;
        }

        $payload = $response['data'] ?? $response;

        return VideoAgentResource::query()->updateOrCreate(
            [
                'session_id'         => $model->getKey(),
                'heygen_resource_id' => $resourceId,
            ],
            [
                'type'       => $payload['type'] ?? VideoAgentResource::TYPE_STORYBOARD,
                'payload'    => $payload,
                'fetched_at' => now(),
            ],
        );
    }

    /**
     * HeyGen has moved the storyboard reference around between shapes, so look
     * in every place it has been published.
     *
     * @param  array<string, mixed>  $session
     */
    private function resourceIdFrom(array $session): ?string
    {
        $candidates = [
            $session['storyboard_id'] ?? null,
            $session['resource_id'] ?? null,
            $session['storyboard']['resource_id'] ?? null,
            $session['storyboard']['id'] ?? null,
        ];

        foreach ($session['resources'] ?? [] as $resource) {
            if (! is_array($resource)) {
                continue;
            }

            if (($resource['type'] ?? null) === VideoAgentResource::TYPE_STORYBOARD) {
                $candidates[] = $resource['resource_id'] ?? $resource['id'] ?? null;
            }
        }

        foreach ($candidates as $candidate) {
            if (filled($candidate)) {
                return (string) $candidate;
            }
        }

        return null;
    }
}
