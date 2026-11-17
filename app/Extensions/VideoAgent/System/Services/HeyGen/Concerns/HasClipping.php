<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Services\HeyGen\Concerns;

/**
 * The resource is `ai-clipping`, singular. The plural form 404s.
 */
trait HasClipping
{
    /**
     * Turn a long video into short clips with captions.
     *
     * @param  array<string, mixed>  $payload
     *
     * @return array<string, mixed>
     */
    public function createAiClipping(array $payload): array
    {
        return $this->post('/v3/ai-clipping', $payload);
    }

    /**
     * @return array<string, mixed>
     */
    public function getAiClipping(string $clippingId): array
    {
        return $this->get("/v3/ai-clipping/{$clippingId}");
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listAiClippings(int $limit = 50): array
    {
        return $this->paginate('/v3/ai-clipping', ['limit' => $limit]);
    }

    /**
     * @return array<string, mixed>
     */
    public function deleteAiClipping(string $clippingId): array
    {
        return $this->delete("/v3/ai-clipping/{$clippingId}");
    }

    /**
     * Strip "um", "uh" and similar from a recording. Billed with a one minute
     * minimum.
     *
     * @param  array<string, mixed>  $payload
     *
     * @return array<string, mixed>
     */
    public function createFillerWordRemoval(array $payload): array
    {
        return $this->post('/v3/filler-word-removals', $payload);
    }

    /**
     * @return array<string, mixed>
     */
    public function getFillerWordRemoval(string $id): array
    {
        return $this->get("/v3/filler-word-removals/{$id}");
    }
}
