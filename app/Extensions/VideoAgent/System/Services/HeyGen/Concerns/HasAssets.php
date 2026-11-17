<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Services\HeyGen\Concerns;

trait HasAssets
{
    /**
     * Upload a file for use as a Video Agent reference or a video input.
     * 32MB cap; png, jpeg, mp4, webm, mp3, wav, pdf and srt.
     *
     * @return array<string, mixed>
     */
    public function uploadAsset(string $contents, string $filename, string $mimeType): array
    {
        return $this->upload('/v3/assets', $contents, $filename, $mimeType);
    }

    /**
     * @return array<string, mixed>
     */
    public function getAsset(string $assetId): array
    {
        return $this->get("/v3/assets/{$assetId}");
    }

    /**
     * @return array<string, mixed>
     */
    public function deleteAsset(string $assetId): array
    {
        return $this->delete("/v3/assets/{$assetId}");
    }
}
