<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Services\HeyGen\Concerns;

trait HasHyperFrames
{
    /**
     * Render a HyperFrames composition zip (HeyGen's HTML-to-video framework,
     * the same engine the Video Agent composes with) into a video.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function createHyperFramesRender(array $payload): array
    {
        return $this->post('/v3/hyperframes/renders', $payload);
    }

    /**
     * @return array<string, mixed>
     */
    public function getHyperFramesRender(string $renderId): array
    {
        return $this->get("/v3/hyperframes/renders/{$renderId}");
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listHyperFramesRenders(int $limit = 50): array
    {
        return $this->paginate('/v3/hyperframes/renders', ['limit' => $limit]);
    }

    /**
     * @return array<string, mixed>
     */
    public function deleteHyperFramesRender(string $renderId): array
    {
        return $this->delete("/v3/hyperframes/renders/{$renderId}");
    }
}
