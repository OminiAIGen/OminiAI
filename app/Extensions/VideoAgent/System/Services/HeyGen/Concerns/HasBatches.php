<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Services\HeyGen\Concerns;

/**
 * Batching hangs off the resource, not off a `/batches` namespace: it is
 * `/v3/videos/batches`, never `/v3/batches/videos`. The inverted form 404s.
 */
trait HasBatches
{
    /**
     * Up to 100 video payloads in one call.
     *
     * @param  array<string, mixed>  $payload
     *
     * @return array<string, mixed>
     */
    public function createVideoBatch(array $payload): array
    {
        return $this->post('/v3/videos/batches', $payload);
    }

    /**
     * @return array<string, mixed>
     */
    public function getVideoBatch(string $batchId): array
    {
        return $this->get("/v3/videos/batches/{$batchId}");
    }

    /**
     * @param  array<string, mixed>  $payload
     *
     * @return array<string, mixed>
     */
    public function createTranslationBatch(array $payload): array
    {
        return $this->post('/v3/video-translations/batches', $payload);
    }

    /**
     * @param  array<string, mixed>  $payload
     *
     * @return array<string, mixed>
     */
    public function createLipsyncBatch(array $payload): array
    {
        return $this->post('/v3/lipsyncs/batches', $payload);
    }

    /**
     * Bulk status is the cheap way to refresh many renders in one call, which
     * is what the poll command uses when a batch is outstanding.
     *
     * It is a GET with comma-separated ids, not a POST with a list, and it
     * takes at most 100 ids. Unknown or unowned ids come back as `not_found`
     * entries rather than failing the whole request.
     *
     * @param  array<int, string>  $videoIds
     *
     * @return array<string, mixed>
     */
    public function bulkVideoStatuses(array $videoIds): array
    {
        return $this->get('/v3/videos/statuses', ['video_ids' => $this->idList($videoIds)]);
    }

    /**
     * Every entry comes back keyed as `video_id` even here — HeyGen shares one
     * status read model across resources.
     *
     * @param  array<int, string>  $lipsyncIds
     *
     * @return array<string, mixed>
     */
    public function bulkLipsyncStatuses(array $lipsyncIds): array
    {
        return $this->get('/v3/lipsyncs/statuses', ['lipsync_ids' => $this->idList($lipsyncIds)]);
    }

    /**
     * @param  array<int, string>  $ids
     */
    private function idList(array $ids): string
    {
        return implode(',', array_slice(array_values(array_unique($ids)), 0, 100));
    }
}
