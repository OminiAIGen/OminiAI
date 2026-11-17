<?php

declare(strict_types=1);

namespace App\Extensions\AutoEditVideos\System\Services\Rendley\Concerns;

trait HasProjects
{
    /**
     * @return array<string, mixed>
     */
    public function project(string $projectId): array
    {
        return $this->get('/v1/projects/' . rawurlencode($projectId));
    }

    /**
     * @return array<string, mixed>
     */
    public function deleteProject(string $projectId): array
    {
        return $this->delete('/v1/projects/' . rawurlencode($projectId));
    }
}
