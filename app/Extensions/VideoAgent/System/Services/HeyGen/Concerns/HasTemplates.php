<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Services\HeyGen\Concerns;

/**
 * Studio templates are authored in HeyGen's web editor, never over the API:
 * there is no endpoint for HeyGen's own preset gallery, and `GET /v3/templates`
 * lists only workspace templates that already have variables defined. A preset
 * duplicated from the gallery stays invisible here until someone opens it in
 * Studio and names its variables.
 */
trait HasTemplates
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function listTemplates(int $limit = 50): array
    {
        return $this->paginate('/v3/templates', ['limit' => $limit]);
    }

    /**
     * Includes the variable definitions the template expects.
     *
     * @return array<string, mixed>
     */
    public function getTemplate(string $templateId): array
    {
        return $this->get("/v3/templates/{$templateId}");
    }

    /**
     * Rendering posts to the template itself. There is no `/generate` suffix —
     * that path 404s, which is how every template render failed silently.
     *
     * @param  array<string, mixed>  $payload
     *
     * @return array<string, mixed>
     */
    public function generateFromTemplate(string $templateId, array $payload): array
    {
        return $this->post("/v3/templates/{$templateId}", $payload);
    }
}
