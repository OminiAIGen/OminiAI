<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Services\HeyGen\Concerns;

trait HasLipsync
{
    /**
     * Dub or replace the audio on an existing video. `mode` is speed or
     * precision, which differ in cost per second.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function createLipsync(array $payload): array
    {
        return $this->post('/v3/lipsyncs', $payload);
    }

    /**
     * @return array<string, mixed>
     */
    public function getLipsync(string $lipsyncId): array
    {
        return $this->get("/v3/lipsyncs/{$lipsyncId}");
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listLipsyncs(int $limit = 50): array
    {
        return $this->paginate('/v3/lipsyncs', ['limit' => $limit]);
    }

    /**
     * @return array<string, mixed>
     */
    public function deleteLipsync(string $lipsyncId): array
    {
        return $this->delete("/v3/lipsyncs/{$lipsyncId}");
    }
}
