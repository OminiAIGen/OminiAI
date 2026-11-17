<?php

declare(strict_types=1);

namespace App\Extensions\AutoEditVideos\System\Services\Rendley\Concerns;

trait HasJobs
{
    /**
     * Read a job. Exports are created on `base_url`, so that is the default,
     * but an agent job is created on the agent host and has to be read there.
     *
     * @return array<string, mixed>
     */
    public function job(string $jobId, ?string $baseUrl = null): array
    {
        $path = '/v1/jobs/' . rawurlencode($jobId);

        return $baseUrl === null
            ? $this->get($path)
            : $this->getAbsolute(rtrim($baseUrl, '/') . $path);
    }

    /**
     * Read a job started by the agent.
     *
     * Rendley's own docs disagree about which host lists it — the automated
     * editing guide polls the agent host, the jobs reference polls the API
     * host. Both answer `/v1/jobs/{id}`, so ask the host that started the job
     * and fall back to the other on a 404 rather than letting a documentation
     * inconsistency strand every session until the attempt ceiling.
     *
     * @return array<string, mixed>
     */
    public function agentJob(string $jobId): array
    {
        $agentBase = $this->agentBaseUrl();
        $apiBase = rtrim((string) config('auto-edit-videos.api.base_url'), '/');

        $job = $this->job($jobId, $agentBase);

        if ($agentBase !== $apiBase && $this->failed($job) && ($job['status'] ?? null) === 404) {
            return $this->job($jobId, $apiBase);
        }

        return $job;
    }

    /**
     * @return array<string, mixed>
     */
    public function cancelJob(string $jobId): array
    {
        return $this->delete('/v1/jobs/' . rawurlencode($jobId));
    }

    /**
     * Rendley sends `result_data` as either an object or a JSON string
     * depending on the job type, so normalise it before anyone reads a key.
     *
     * @param  array<string, mixed>  $job
     *
     * @return array<string, mixed>
     */
    public function resultData(array $job): array
    {
        $result = $job['result_data'] ?? [];

        if (is_string($result)) {
            $decoded = json_decode($result, true);
            $result = is_array($decoded) ? $decoded : [];
        }

        return is_array($result) ? $result : [];
    }
}
