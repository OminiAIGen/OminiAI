<?php

declare(strict_types=1);

namespace App\Extensions\AutoEditVideos\System\Services\Rendley\Concerns;

trait HasExports
{
    /**
     * Price a render without spending anything. Rendley accepts the same body
     * as `/v1/export`, so the two calls cannot disagree about what is queued.
     *
     * @param  array<string, mixed>  $settings
     *
     * @return array<string, mixed>
     */
    public function exportCost(string $projectId, array $settings = []): array
    {
        return $this->post('/v1/export/cost', $this->exportPayload($projectId, $settings));
    }

    /**
     * @param  array<string, mixed>  $settings
     *
     * @return array<string, mixed>
     */
    public function export(string $projectId, array $settings = []): array
    {
        return $this->post('/v1/export', $this->exportPayload($projectId, $settings));
    }

    /**
     * @param  array<string, mixed>  $settings
     *
     * @return array{project_id: string, settings: array<string, mixed>}
     */
    protected function exportPayload(string $projectId, array $settings): array
    {
        $defaults = [
            'target_resolution' => (string) config('auto-edit-videos.defaults.target_resolution', '1080p'),
            'codec'             => (string) config('auto-edit-videos.defaults.codec', 'h264'),
            'quality'           => (string) config('auto-edit-videos.defaults.quality', 'high'),
        ];

        return [
            'project_id' => $projectId,
            'settings'   => array_filter($settings + $defaults, static fn ($value) => $value !== null && $value !== ''),
        ];
    }
}
