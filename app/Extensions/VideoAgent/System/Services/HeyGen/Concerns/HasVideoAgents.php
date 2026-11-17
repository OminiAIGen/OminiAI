<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Services\HeyGen\Concerns;

trait HasVideoAgents
{
    /**
     * Create a Video Agent session. `mode: chat` adds a storyboard review
     * checkpoint; `mode: generate` renders straight away.
     *
     * @param  array{
     *     prompt: string,
     *     mode?: string,
     *     avatar_id?: string|null,
     *     voice_id?: string|null,
     *     style_id?: string|null,
     *     brand_kit_id?: string|null,
     *     orientation?: string|null,
     *     files?: array<int, array<string, mixed>>,
     *     callback_url?: string|null,
     *     callback_id?: string|null,
     *     incognito_mode?: bool,
     * }  $payload
     *
     * @return array<string, mixed>
     */
    public function createVideoAgentSession(array $payload): array
    {
        return $this->post('/v3/video-agents', $payload);
    }

    /**
     * @return array<string, mixed>
     */
    public function getVideoAgentSession(string $sessionId): array
    {
        return $this->get("/v3/video-agents/{$sessionId}");
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listVideoAgentSessions(int $limit = 20): array
    {
        return $this->paginate('/v3/video-agents', ['limit' => $limit]);
    }

    /**
     * @return array<string, mixed>
     */
    public function listVideoAgentSessionVideos(string $sessionId): array
    {
        return $this->get("/v3/video-agents/{$sessionId}/videos");
    }

    /**
     * Send feedback, request a revision, or approve the storyboard.
     *
     * @param  array<string, mixed>  $payload
     *
     * @return array<string, mixed>
     */
    public function sendVideoAgentMessage(string $sessionId, array $payload): array
    {
        return $this->post("/v3/video-agents/{$sessionId}", $payload);
    }

    /**
     * @return array<string, mixed>
     */
    public function stopVideoAgentSession(string $sessionId): array
    {
        return $this->post("/v3/video-agents/{$sessionId}/stop", [], false);
    }

    /**
     * Storyboards, drafts, avatars and voices produced during a session.
     *
     * @return array<string, mixed>
     */
    public function getVideoAgentResource(string $sessionId, string $resourceId): array
    {
        return $this->get("/v3/video-agents/{$sessionId}/resources/{$resourceId}");
    }

    /**
     * Look Packs. Cursor paginated, filterable by tag.
     *
     * @return array<int, array<string, mixed>>
     */
    public function listVideoAgentStyles(int $limit = 50): array
    {
        return $this->paginate('/v3/video-agents/styles', ['limit' => $limit]);
    }

    /**
     * @param  callable(array<int, array<string, mixed>>): void  $onPage
     */
    public function eachVideoAgentStylePage(callable $onPage, int $limit = 100, int $maxPages = 20): bool
    {
        return $this->paginateEach('/v3/video-agents/styles', ['limit' => $limit], $onPage, $maxPages);
    }
}
