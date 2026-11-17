<?php

declare(strict_types=1);

namespace App\Extensions\AutoEditVideos\System\Services\Rendley\Concerns;

trait HasAgent
{
    /**
     * Hand Rendley a prompt and the source footage and let its agent plan the
     * cut. Passing `project_id` and `thread_id` continues an existing edit
     * instead of starting a new one, which is what a follow-up message does.
     *
     * @param  array<int, string>  $fileUrls
     *
     * @return array<string, mixed>
     */
    public function startAgent(string $prompt, array $fileUrls = [], ?string $projectId = null, ?string $threadId = null): array
    {
        $payload = array_filter([
            'prompt'     => $prompt,
            'files'      => array_values(array_map(static fn (string $url): array => ['url' => $url], $fileUrls)),
            'project_id' => $projectId,
            'thread_id'  => $threadId,
        ], static fn ($value) => $value !== null && $value !== '' && $value !== []);

        return $this->postAbsolute($this->agentUrl(), $payload);
    }

    /**
     * Scheme, host and port of the agent endpoint, so a job the agent started
     * can be polled on the host that owns it.
     */
    public function agentBaseUrl(): string
    {
        $parts = parse_url($this->agentUrl());
        $host = is_array($parts) ? ($parts['host'] ?? null) : null;

        if (blank($host)) {
            return rtrim((string) config('auto-edit-videos.api.base_url'), '/');
        }

        $port = isset($parts['port']) ? ':' . $parts['port'] : '';

        return ($parts['scheme'] ?? 'https') . '://' . $host . $port;
    }

    /**
     * The auto-edit agent is not served from `base_url` — it lives on its own
     * host (`mcp.rendley.com`), while exports, jobs and account calls live on
     * `api.rendley.com`. Keeping the whole URL settable lets an administrator
     * follow a vendor change without a deploy.
     */
    protected function agentUrl(): string
    {
        $override = trim((string) setting('auto_edit_videos_agent_url', ''));

        return $override !== ''
            ? $override
            : (string) config('auto-edit-videos.api.agent_url');
    }
}
