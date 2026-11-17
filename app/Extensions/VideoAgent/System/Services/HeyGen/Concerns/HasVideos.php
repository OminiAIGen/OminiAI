<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Services\HeyGen\Concerns;

trait HasVideos
{
    /**
     * `POST /v3/videos` — discriminated by `type`: avatar, image,
     * cinematic_avatar or studio.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function createVideo(array $payload): array
    {
        return $this->post('/v3/videos', $payload);
    }

    /**
     * @return array<string, mixed>
     */
    public function getVideo(string $videoId): array
    {
        return $this->get("/v3/videos/{$videoId}");
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listVideos(int $limit = 50): array
    {
        return $this->paginate('/v3/videos', ['limit' => $limit]);
    }

    /**
     * @return array<string, mixed>
     */
    public function deleteVideo(string $videoId): array
    {
        return $this->delete("/v3/videos/{$videoId}");
    }
}
