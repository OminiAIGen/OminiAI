<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Services\HeyGen\Concerns;

trait HasVoices
{
    /**
     * @param  array{type?: string, engine?: string, language?: string, gender?: string}  $filters
     *
     * @return array<int, array<string, mixed>>
     */
    public function listVoices(array $filters = [], int $limit = 100): array
    {
        return $this->paginate('/v3/voices', $filters + ['limit' => $limit]);
    }

    /**
     * @param  callable(array<int, array<string, mixed>>): void  $onPage
     * @param  array{type?: string, engine?: string, language?: string, gender?: string}  $filters
     */
    public function eachVoicePage(callable $onPage, array $filters = [], int $limit = 100, int $maxPages = 20): bool
    {
        return $this->paginateEach('/v3/voices', $filters + ['limit' => $limit], $onPage, $maxPages);
    }

    /**
     * @return array<string, mixed>
     */
    public function getVoice(string $voiceId): array
    {
        return $this->get("/v3/voices/{$voiceId}");
    }

    /**
     * @param  array<string, mixed>  $payload
     *
     * @return array<string, mixed>
     */
    public function cloneVoice(array $payload): array
    {
        return $this->post('/v3/voices/clone', $payload);
    }

    /**
     * Returns up to three voices matching a natural-language description. It is
     * a plain `POST /v3/voices` — HeyGen has no `/v3/voices/design` path, which
     * is why every design request 404ed.
     *
     * @param  array<string, mixed>  $payload
     *
     * @return array<string, mixed>
     */
    public function designVoice(array $payload): array
    {
        return $this->post('/v3/voices', $payload);
    }

    /**
     * @return array<string, mixed>
     */
    public function deleteVoice(string $voiceId): array
    {
        return $this->delete("/v3/voices/{$voiceId}");
    }

    /**
     * Standalone text to speech.
     *
     * @param  array<string, mixed>  $payload
     *
     * @return array<string, mixed>
     */
    public function generateSpeech(array $payload): array
    {
        return $this->post('/v3/voices/speech', $payload);
    }
}
