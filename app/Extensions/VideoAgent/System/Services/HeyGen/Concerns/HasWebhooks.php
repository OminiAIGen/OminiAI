<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Services\HeyGen\Concerns;

trait HasWebhooks
{
    /**
     * Register a webhook endpoint. The signing secret is returned once only.
     *
     * @param  array<int, string>  $events
     * @return array<string, mixed>
     */
    public function createWebhookEndpoint(string $url, array $events = []): array
    {
        return $this->post('/v3/webhooks/endpoints', array_filter([
            'url'    => $url,
            'events' => $events ?: null,
        ]));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listWebhookEndpoints(): array
    {
        return $this->paginate('/v3/webhooks/endpoints');
    }

    /**
     * @return array<string, mixed>
     */
    public function deleteWebhookEndpoint(string $endpointId): array
    {
        return $this->delete("/v3/webhooks/endpoints/{$endpointId}");
    }

    /**
     * @return array<string, mixed>
     */
    public function rotateWebhookSecret(string $endpointId): array
    {
        return $this->post("/v3/webhooks/endpoints/{$endpointId}/rotate-secret", [], false);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listWebhookEventTypes(): array
    {
        return $this->paginate('/v3/webhooks/event-types');
    }
}
