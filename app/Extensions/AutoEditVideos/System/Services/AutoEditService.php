<?php

declare(strict_types=1);

namespace App\Extensions\AutoEditVideos\System\Services;

use App\Extensions\AutoEditVideos\System\Enums\AutoEditKindEnum;
use App\Extensions\AutoEditVideos\System\Enums\RenderStatusEnum;
use App\Extensions\AutoEditVideos\System\Enums\SessionStatusEnum;
use App\Extensions\AutoEditVideos\System\Jobs\DownloadAutoEditOutputJob;
use App\Extensions\AutoEditVideos\System\Jobs\RefreshAutoEditRenderJob;
use App\Extensions\AutoEditVideos\System\Jobs\RefreshAutoEditSessionJob;
use App\Extensions\AutoEditVideos\System\Models\AutoEditMessage;
use App\Extensions\AutoEditVideos\System\Models\AutoEditRender;
use App\Extensions\AutoEditVideos\System\Models\AutoEditSession;
use App\Extensions\AutoEditVideos\System\Models\AutoEditSource;
use App\Extensions\AutoEditVideos\System\Services\Rendley\RendleyClient;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class AutoEditService
{
    public function __construct(
        private readonly RendleyClient $client,
        private readonly SourceMediaService $sources,
        private readonly AutoEditBillingService $billing,
    ) {}

    /**
     * Rendley takes no duration, style or aspect parameters — its guide is
     * explicit that all three belong in the prompt (e.g. "Reframe to 9:16
     * for Reels, keeping the speaker centered") — so fold them in here.
     * Export settings only accept target_resolution/codec/quality, so a
     * 9:16 choice saved on the session would otherwise be dead data and
     * Rendley would default the timeline to 16:9 landscape.
     */
    public static function composePrompt(string $prompt, ?int $duration = null, ?string $instructions = null, ?string $aspectRatio = null): string
    {
        $parts = [trim($prompt)];

        if (filled($aspectRatio)) {
            $parts[] = self::aspectInstruction(trim((string) $aspectRatio));
        }

        if ($duration !== null && $duration > 0) {
            $parts[] = __('Target length: about :seconds seconds.', ['seconds' => $duration]);
        }

        if (filled($instructions)) {
            $parts[] = trim((string) $instructions);
        }

        return implode("\n\n", array_filter($parts, static fn (string $part): bool => $part !== ''));
    }

    private static function aspectInstruction(string $aspectRatio): string
    {
        return match ($aspectRatio) {
            '9:16'  => __('Output format: 9:16 vertical (1080x1920) for Reels, TikTok and Shorts. Reframe the edit to vertical, keep the main subject centered, fill the frame. Do not letterbox, pillarbox or output 16:9 landscape.'),
            '16:9'  => __('Output format: 16:9 landscape (1920x1080). Frame the edit for widescreen. Do not output vertical video.'),
            '1:1'   => __('Output format: 1:1 square (1080x1080). Reframe the edit to square, keep the main subject centered. Do not output 16:9 landscape.'),
            '4:5'   => __('Output format: 4:5 portrait (1080x1350). Reframe the edit to vertical 4:5, keep the main subject centered. Do not output 16:9 landscape.'),
            default => __('Output format: :ratio. Reframe the whole edit to that aspect ratio and do not output 16:9 unless asked.', ['ratio' => $aspectRatio]),
        };
    }

    /**
     * @param  array{
     *     prompt: string, instructions?: string|null, duration?: int|null,
     *     target_resolution?: string|null, aspect_ratio?: string|null,
     *     source_ids?: array<int, int>
     * }  $input
     *
     * @return AutoEditSession|string the session, or the reason it was refused
     */
    public function start(User $user, array $input): AutoEditSession|string
    {
        $prompt = trim($input['prompt']);
        $duration = isset($input['duration']) && $input['duration'] !== '' ? (int) $input['duration'] : null;
        $instructions = filled($input['instructions'] ?? null) ? trim((string) $input['instructions']) : null;

        $sources = AutoEditSource::query()
            ->where('user_id', $user->id)
            ->whereNull('session_id')
            ->whereIn('id', $input['source_ids'] ?? [])
            ->get();

        if ($sources->isEmpty()) {
            return __('Add at least one video, audio or image file before starting an edit.');
        }

        $estimate = $this->billing->estimateSeconds($duration, $this->sources->totalSeconds($sources));

        $allowed = $this->billing->assertCanStart($user, $estimate);

        if ($allowed !== true) {
            return $allowed;
        }

        $session = AutoEditSession::query()->create([
            'user_id'      => $user->id,
            'team_id'      => $user->team_id ?? null,
            // The raw prompt, so the thread shows what the user typed rather
            // than the scaffolding we add on the way to Rendley.
            'prompt'            => $prompt,
            'title'             => Str::limit($prompt, 80),
            'instructions'      => $instructions,
            'duration_seconds'  => $duration,
            'target_resolution' => $input['target_resolution'] ?? config('auto-edit-videos.defaults.target_resolution'),
            'aspect_ratio'      => $input['aspect_ratio'] ?? config('auto-edit-videos.defaults.aspect_ratio'),
            'status'            => SessionStatusEnum::Planning,
        ]);

        $sources->each(fn (AutoEditSource $source) => $source->forceFill(['session_id' => $session->id])->save());

        $session->messages()->create([
            'role' => AutoEditMessage::ROLE_USER,
            'kind' => AutoEditMessage::KIND_MESSAGE,
            'body' => $prompt,
        ]);

        $response = $this->client->startAgent(
            self::composePrompt($prompt, $duration, $instructions, $input['aspect_ratio'] ?? config('auto-edit-videos.defaults.aspect_ratio')),
            $this->sources->urlsFor($sources),
        );

        if ($this->client->failed($response)) {
            $session->forceFill([
                'status'        => SessionStatusEnum::Failed,
                'error_message' => $response['message'] ?? __('Rendley rejected the request.'),
            ])->save();

            return (string) ($response['message'] ?? __('Rendley rejected the request.'));
        }

        $session->forceFill([
            'rendley_job_id'     => $response['job_id'] ?? null,
            'rendley_project_id' => $response['project_id'] ?? null,
            'rendley_thread_id'  => $response['thread_id'] ?? null,
        ])->save();

        RefreshAutoEditSessionJob::dispatch($session->getKey());

        return $session->refresh();
    }

    /**
     * Continue an existing edit. Reusing the project and thread is what lets
     * Rendley act on "make it shorter" without being re-told the whole brief.
     */
    public function sendMessage(AutoEditSession $session, string $body): true|string
    {
        if (blank($session->rendley_project_id) || blank($session->rendley_thread_id)) {
            return __('This edit has not finished planning yet.');
        }

        $response = $this->client->startAgent(
            trim($body),
            [],
            (string) $session->rendley_project_id,
            (string) $session->rendley_thread_id,
        );

        if ($this->client->failed($response)) {
            return (string) ($response['message'] ?? __('Rendley rejected the message.'));
        }

        $session->messages()->create([
            'role'           => AutoEditMessage::ROLE_USER,
            'kind'           => AutoEditMessage::KIND_MESSAGE,
            'body'           => trim($body),
            'rendley_job_id' => $response['job_id'] ?? null,
        ]);

        $session->forceFill([
            'rendley_job_id' => $response['job_id'] ?? $session->rendley_job_id,
            'status'         => SessionStatusEnum::Planning,
            'poll_attempts'  => 0,
            'last_polled_at' => null,
            'error_message'  => null,
        ])->save();

        RefreshAutoEditSessionJob::dispatch($session->getKey());

        return true;
    }

    /**
     * Pull the latest agent job state for a session.
     */
    public function syncSession(AutoEditSession $session): AutoEditSession
    {
        if (blank($session->rendley_job_id) || ! $session->status->isAwaitingAgent()) {
            return $session;
        }

        // Started on the agent host, so it has to be read back from there.
        $job = $this->client->agentJob((string) $session->rendley_job_id);

        $session->forceFill([
            'poll_attempts'  => $session->poll_attempts + 1,
            'last_polled_at' => now(),
        ])->save();

        if ($this->client->failed($job)) {
            return $session;
        }

        $status = RenderStatusEnum::fromApi($job['status'] ?? null);

        if (! $status->isTerminal()) {
            return $session;
        }

        if ($status === RenderStatusEnum::Completed) {
            $result = $this->client->resultData($job);

            $session->forceFill([
                'rendley_project_id' => $result['project_id'] ?? $session->rendley_project_id,
                'rendley_thread_id'  => $result['thread_id'] ?? $session->rendley_thread_id,
                'project_url'        => $result['project_url'] ?? $session->project_url,
                'status'             => SessionStatusEnum::Ready,
                'meta'               => $result,
            ])->save();

            $this->recordAgentReply($session, $result);

            return $session->refresh();
        }

        $session->forceFill([
            'status'        => $status === RenderStatusEnum::Canceled ? SessionStatusEnum::Canceled : SessionStatusEnum::Failed,
            'error_message' => $this->errorFrom($job),
        ])->save();

        return $session->refresh();
    }

    /**
     * What Rendley would charge for this export, in Rendley credits.
     *
     * @return array{credits: float}|array{error: string, message: string}
     */
    public function estimateExportCost(AutoEditSession $session): array
    {
        if (blank($session->rendley_project_id)) {
            return ['error' => 'not_ready', 'message' => __('This edit has not finished planning yet.')];
        }

        $response = $this->client->exportCost((string) $session->rendley_project_id, [
            'target_resolution' => $session->target_resolution,
        ]);

        if ($this->client->failed($response)) {
            return ['error' => 'request_failed', 'message' => $this->client->messageFrom($response)];
        }

        return ['credits' => (float) ($response['credits'] ?? 0)];
    }

    /**
     * Queue a render of the session's current project.
     *
     * @return AutoEditRender|string the render, or the reason it was refused
     */
    public function requestExport(AutoEditSession $session): AutoEditRender|string
    {
        $user = $session->user;

        if ($user === null) {
            return __('This edit no longer has an owner.');
        }

        if (blank($session->rendley_project_id)) {
            return __('This edit has not finished planning yet.');
        }

        $estimate = $this->billing->estimateSeconds(
            $session->duration_seconds,
            $this->sources->totalSeconds($session->sources),
        );

        $allowed = $this->billing->assertCanStart($user, $estimate);

        if ($allowed !== true) {
            return $allowed;
        }

        // Price the exact job before queueing it, so a user is never charged
        // for a render they could not have afforded.
        $cost = $this->estimateExportCost($session);

        if (isset($cost['error'])) {
            return (string) $cost['message'];
        }

        $response = $this->client->export((string) $session->rendley_project_id, [
            'target_resolution' => $session->target_resolution,
        ]);

        if ($this->client->failed($response)) {
            return (string) ($response['message'] ?? __('Rendley rejected the render.'));
        }

        $render = AutoEditRender::query()->create([
            'session_id'        => $session->id,
            'user_id'           => $user->id,
            'team_id'           => $session->team_id,
            'kind'              => AutoEditKindEnum::AutoEdit,
            'rendley_job_id'    => $response['job_id'] ?? null,
            'title'             => $session->displayTitle(),
            'status'            => RenderStatusEnum::Queued,
            'target_resolution' => $session->target_resolution,
            'rendley_credits'   => $cost['credits'] ?? null,
        ]);

        $session->forceFill(['status' => SessionStatusEnum::Rendering])->save();

        RefreshAutoEditRenderJob::dispatch($render->getKey());

        return $render;
    }

    /**
     * Pull the latest export job state for a render.
     */
    public function syncRender(AutoEditRender $render): AutoEditRender
    {
        if (blank($render->rendley_job_id) || $render->isTerminal()) {
            return $render;
        }

        $job = $this->client->job((string) $render->rendley_job_id);

        $render->forceFill([
            'poll_attempts'  => $render->poll_attempts + 1,
            'last_polled_at' => now(),
        ])->save();

        if ($this->client->failed($job)) {
            return $render;
        }

        $status = RenderStatusEnum::fromApi($job['status'] ?? null);

        if (! $status->isTerminal()) {
            $render->forceFill(['status' => $status])->save();

            return $render;
        }

        if ($status === RenderStatusEnum::Completed) {
            return $this->completeRender($render, $this->client->resultData($job));
        }

        $render->forceFill([
            'status'        => $status,
            'error_message' => $this->errorFrom($job),
        ])->save();

        $render->session?->forceFill([
            'status'        => SessionStatusEnum::Ready,
            'error_message' => $this->errorFrom($job),
        ])->save();

        return $render->refresh();
    }

    /**
     * @param  iterable<AutoEditRender>  $renders
     */
    public function syncRendersInBulk(iterable $renders): void
    {
        foreach ($renders as $render) {
            $this->syncRender($render);
        }
    }

    /**
     * @param  array<string, mixed>  $result
     */
    private function completeRender(AutoEditRender $render, array $result): AutoEditRender
    {
        $storageUrl = $result['storage_url'] ?? null;

        if (! is_string($storageUrl) || $storageUrl === '') {
            $render->forceFill([
                'status'        => RenderStatusEnum::Failed,
                'error_message' => __('Rendley finished the render but returned no file.'),
            ])->save();

            return $render->refresh();
        }

        // Claim the transition with a conditional update so two concurrent
        // pollers cannot both fall through and debit the user twice.
        $claimed = AutoEditRender::query()
            ->whereKey($render->getKey())
            ->whereIn('status', [RenderStatusEnum::Queued->value, RenderStatusEnum::Processing->value])
            ->update([
                'status'           => RenderStatusEnum::Completed->value,
                'remote_url'       => $storageUrl,
                'thumbnail_url'    => Arr::get($result, 'thumbnail_url'),
                'duration_seconds' => $this->durationFrom($result, $render),
                'completed_at'     => now(),
                'payload'          => json_encode($result),
                'updated_at'       => now(),
            ]);

        $render = $render->refresh();

        if ($claimed === 0) {
            return $render;
        }

        $render->session?->forceFill(['status' => SessionStatusEnum::Completed])->save();

        $this->billing->debit($render);

        // Rendley's storage URLs are time limited, so take our own copy now.
        DownloadAutoEditOutputJob::dispatch($render->getKey(), $storageUrl);

        return $render->refresh();
    }

    /**
     * @param  array<string, mixed>  $result
     */
    private function durationFrom(array $result, AutoEditRender $render): int
    {
        $duration = $result['duration'] ?? $result['duration_seconds'] ?? null;

        if (is_numeric($duration) && (int) ceil((float) $duration) > 0) {
            return (int) ceil((float) $duration);
        }

        if ((int) $render->duration_seconds > 0) {
            return (int) $render->duration_seconds;
        }

        // Never bill zero: fall back to what the user asked for, then to the
        // same floor the pre-flight estimate uses.
        return max(1, (int) ($render->session?->duration_seconds ?: 60));
    }

    /**
     * @param  array<string, mixed>  $result
     */
    private function recordAgentReply(AutoEditSession $session, array $result): void
    {
        $body = $result['message'] ?? $result['summary'] ?? __('Your edit is ready to export.');

        $session->messages()->create([
            'role'           => AutoEditMessage::ROLE_AGENT,
            'kind'           => AutoEditMessage::KIND_PLAN,
            'body'           => is_string($body) ? $body : json_encode($body),
            'rendley_job_id' => $session->rendley_job_id,
            'meta'           => $result,
        ]);
    }

    /**
     * @param  array<string, mixed>  $job
     */
    private function errorFrom(array $job): string
    {
        $error = $job['error'] ?? null;

        if (is_string($error) && filled($error)) {
            return $error;
        }

        if (is_array($error) && filled($error['message'] ?? null)) {
            return (string) $error['message'];
        }

        return __('Rendley could not finish this edit.');
    }
}
