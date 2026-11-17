<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Services\HeyGen\Concerns;

trait HasTranslations
{
    /**
     * Video translation is shipped disabled by default: the VideoDubbing
     * extension already owns this workflow. See config('video-agent.features').
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function createVideoTranslation(array $payload): array
    {
        return $this->post('/v3/video-translations', $payload);
    }

    /**
     * @return array<string, mixed>
     */
    public function getVideoTranslation(string $translationId): array
    {
        return $this->get("/v3/video-translations/{$translationId}");
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listTranslationLanguages(): array
    {
        $response = $this->get('/v3/video-translations/languages');

        return $response['data'] ?? [];
    }
}
