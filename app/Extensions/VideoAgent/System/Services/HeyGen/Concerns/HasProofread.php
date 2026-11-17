<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Services\HeyGen\Concerns;

/**
 * Proofreading is part of video translation, and its endpoints live under
 * `/v3/video-translations/proofreads` — there is no top-level
 * `/v3/proofread-sessions`, which returns a 404.
 */
trait HasProofread
{
    /**
     * @param  array<string, mixed>  $payload
     *
     * @return array<string, mixed>
     */
    public function createProofreadSession(array $payload): array
    {
        return $this->post('/v3/video-translations/proofreads', $payload);
    }

    /**
     * @return array<string, mixed>
     */
    public function getProofreadSession(string $sessionId): array
    {
        return $this->get("/v3/video-translations/proofreads/{$sessionId}");
    }

    /**
     * Returns presigned URLs for the edited and original SRT files, not the
     * subtitle text itself.
     *
     * @return array<string, mixed>
     */
    public function downloadProofreadSrt(string $sessionId): array
    {
        return $this->get("/v3/video-translations/proofreads/{$sessionId}/srt");
    }

    /**
     * A full replacement of the subtitles, hence PUT rather than POST.
     *
     * @param  array<string, mixed>  $payload
     *
     * @return array<string, mixed>
     */
    public function uploadProofreadSrt(string $sessionId, array $payload): array
    {
        return $this->put("/v3/video-translations/proofreads/{$sessionId}/srt", $payload);
    }

    /**
     * @param  array<string, mixed>  $payload
     *
     * @return array<string, mixed>
     */
    public function generateVideoFromProofread(string $sessionId, array $payload = []): array
    {
        return $this->post("/v3/video-translations/proofreads/{$sessionId}/generate", $payload);
    }
}
