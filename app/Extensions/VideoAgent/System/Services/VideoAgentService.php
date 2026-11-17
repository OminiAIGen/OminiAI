<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Services;

use App\Extensions\VideoAgent\System\Enums\SessionModeEnum;
use App\Extensions\VideoAgent\System\Enums\SessionStatusEnum;
use App\Extensions\VideoAgent\System\Enums\VideoAgentKindEnum;
use App\Extensions\VideoAgent\System\Enums\VideoStatusEnum;
use App\Extensions\VideoAgent\System\Jobs\DownloadVideoAgentOutputJob;
use App\Extensions\VideoAgent\System\Jobs\RefreshVideoAgentStatusJob;
use App\Extensions\VideoAgent\System\Models\VideoAgentMessage;
use App\Extensions\VideoAgent\System\Models\VideoAgentSession;
use App\Extensions\VideoAgent\System\Models\VideoAgentVideo;
use App\Extensions\VideoAgent\System\Services\HeyGen\HeyGenClient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class VideoAgentService
{
    public function __construct(
        private readonly HeyGenClient $client,
        private readonly StoryboardService $storyboards,
        private readonly VideoAgentBillingService $billing,
    ) {}

    /**
     * HeyGen exposes no duration and no custom-instructions parameter, so both
     * are folded into the prompt. Its own guide is explicit about this: "Put
     * the duration in the prompt, direct the look with a style paragraph."
     */
    public static function composePrompt(string $prompt, ?int $duration = null, ?string $instructions = null): string
    {
        $parts = [trim($prompt)];

        if ($duration !== null) {
            $parts[] = __('Target length: about :seconds seconds.', ['seconds' => $duration]);
        }

        if (filled($instructions)) {
            $parts[] = trim((string) $instructions);
        }

        return implode("\n\n", array_filter($parts, static fn (string $part): bool => $part !== ''));
    }

    public static function isOpeningPromptEcho(VideoAgentSession $session, string $body, ?int $remoteCreatedAt = null): bool
    {
        if ($remoteCreatedAt !== null && abs($remoteCreatedAt - $session->created_at->timestamp) > 300) {
            return false;
        }

        $composed = self::composePrompt($session->prompt, $session->duration_seconds, $session->instructions);

        return $composed !== $session->prompt
            && self::normalizeMessage($body) === self::normalizeMessage($composed);
    }

    private static function normalizeMessage(string $body): string
    {
        return trim(str_replace(["\r\n", "\r"], "\n", $body));
    }

    /**
     * @param  array{
     *     prompt: string, mode?: string, style_id?: string|null,
     *     brand_kit_id?: string|null, avatar_id?: string|null, voice_id?: string|null,
     *     orientation?: string|null, duration?: int|null, instructions?: string|null,
     *     files?: array<int, array<string, mixed>>
     * }  $input
     *
     * @return VideoAgentSession|string the session, or the reason it was refused
     */
    public function start(User $user, array $input): VideoAgentSession|string
    {
        $prompt = trim($input['prompt']);
        $duration = isset($input['duration']) && $input['duration'] !== '' ? (int) $input['duration'] : null;
        $instructions = filled($input['instructions'] ?? null) ? trim((string) $input['instructions']) : null;

        // A chosen length is a fact, not a guess, so prefer it over the
        // word-count estimate the billing service falls back to.
        $estimate = $duration ?? $this->billing->estimateSecondsFromPrompt($prompt);

        $allowed = $this->billing->assertCanStart($user, VideoAgentKindEnum::Agent, $estimate);

        if ($allowed !== true) {
            return $allowed;
        }

        $mode = SessionModeEnum::tryFrom($input['mode'] ?? '')
            ?? SessionModeEnum::from((string) setting('video_agent_default_mode', config('video-agent.defaults.mode')));

        $orientation = $input['orientation']
            ?? setting('video_agent_default_orientation', config('video-agent.defaults.orientation'));

        $session = VideoAgentSession::query()->create([
            'user_id'          => $user->id,
            'team_id'          => $user->team_id ?? null,
            'mode'             => $mode,
            // The raw prompt, so the thread shows what the user typed rather
            // than the scaffolding we add on the way to HeyGen.
            'prompt'           => $prompt,
            'title'            => Str::limit($prompt, 80),
            'style_id'         => $input['style_id'] ?? null,
            'brand_kit_id'     => $input['brand_kit_id'] ?? null,
            'avatar_id'        => $input['avatar_id'] ?? null,
            'voice_id'         => $input['voice_id'] ?? null,
            'orientation'      => filled($orientation) ? $orientation : null,
            'duration_seconds' => $duration,
            'instructions'     => $instructions,
            'resolution'       => setting('video_agent_default_resolution', config('video-agent.defaults.resolution')),
            'status'           => SessionStatusEnum::Thinking,
        ]);

        $session->messages()->create([
            'role' => VideoAgentMessage::ROLE_USER,
            'kind' => VideoAgentMessage::KIND_MESSAGE,
            'body' => $prompt,
        ]);

        $response = $this->client->createVideoAgentSession(array_filter([
            'prompt'       => self::composePrompt($prompt, $duration, $instructions),
            'mode'         => $mode->value,
            'style_id'     => $session->style_id,
            'brand_kit_id' => $session->brand_kit_id,
            'avatar_id'    => $session->avatar_id,
            'voice_id'     => $session->voice_id,
            'orientation'  => $session->orientation,
            'files'        => $input['files'] ?? null,
            'callback_url' => $this->callbackUrl(),
            'callback_id'  => $session->uuid,
            // An empty string is not "unset" to HeyGen — an unselected pill
            // would otherwise post '' and be sent as a real value.
        ], static fn ($value) => $value !== null && $value !== '' && $value !== []));

        if (isset($response['error'])) {
            $session->forceFill([
                'status'        => SessionStatusEnum::Failed,
                'error_message' => $response['message'] ?? __('HeyGen rejected the request.'),
            ])->save();

            return (string) ($response['message'] ?? __('HeyGen rejected the request.'));
        }

        $session->forceFill([
            'heygen_session_id' => $response['session_id'] ?? Arr::get($response, 'data.session_id'),
            'status'            => SessionStatusEnum::fromApi($response['status'] ?? Arr::get($response, 'data.status')),
        ])->save();

        $this->attachVideoIfPresent($session, $response['video_id'] ?? Arr::get($response, 'data.video_id'));

        RefreshVideoAgentStatusJob::dispatch($session->getKey());

        return $session->refresh();
    }

    /**
     * Pull the latest session state, messages, storyboard and video.
     */
    public function sync(VideoAgentSession $session): VideoAgentSession
    {
        if (blank($session->heygen_session_id) || $session->isTerminal()) {
            return $session;
        }

        $response = $this->client->getVideoAgentSession((string) $session->heygen_session_id);

        $session->forceFill([
            'poll_attempts'  => $session->poll_attempts + 1,
            'last_polled_at' => now(),
        ])->save();

        if (isset($response['error'])) {
            return $session;
        }

        $data = $response['data'] ?? $response;

        $session->forceFill([
            'status' => SessionStatusEnum::fromApi($data['status'] ?? null),
            'meta'   => $data,
        ])->save();

        $this->persistMessages($session, $data['messages'] ?? []);
        $this->storyboards->syncFromSessionPayload($session, $data);
        $this->attachVideoIfPresent($session, $data['video_id'] ?? null);

        if ($session->status === SessionStatusEnum::Failed) {
            $session->forceFill([
                'error_message' => $data['error']['message'] ?? $data['error'] ?? __('The agent could not finish this video.'),
            ])->save();
        }

        return $session->refresh();
    }

    public function sendMessage(VideoAgentSession $session, string $body, string $kind = VideoAgentMessage::KIND_MESSAGE): true|string
    {
        if (blank($session->heygen_session_id)) {
            return __('This session has not started yet.');
        }

        $response = $this->client->sendVideoAgentMessage((string) $session->heygen_session_id, [
            'message' => $body,
        ]);

        if (isset($response['error'])) {
            return (string) ($response['message'] ?? __('HeyGen rejected the message.'));
        }

        $session->messages()->create([
            'role' => VideoAgentMessage::ROLE_USER,
            'kind' => $kind,
            'body' => $body,
        ]);

        $session->forceFill(['status' => SessionStatusEnum::Thinking])->save();

        RefreshVideoAgentStatusJob::dispatch($session->getKey());

        return true;
    }

    public function approve(VideoAgentSession $session): true|string
    {
        return $this->sendMessage(
            $session,
            __('Approved. Please render the video.'),
            VideoAgentMessage::KIND_APPROVAL,
        );
    }

    public function stop(VideoAgentSession $session): true|string
    {
        if (filled($session->heygen_session_id)) {
            $response = $this->client->stopVideoAgentSession((string) $session->heygen_session_id);

            if (isset($response['error'])) {
                return (string) ($response['message'] ?? __('HeyGen could not stop this session.'));
            }
        }

        $session->forceFill(['status' => SessionStatusEnum::Stopped])->save();

        $session->videos()->unfinished()->update([
            'status'        => VideoStatusEnum::Failed->value,
            'error_message' => __('Session stopped before the render finished.'),
        ]);

        return true;
    }

    /**
     * Translation and proofread renders are not videos as far as HeyGen is
     * concerned: their ids only resolve under `/v3/video-translations`, and
     * polling them at `/v3/videos/{id}` answers 404 forever, so the job never
     * leaves `processing`.
     *
     * @return array<string, mixed>
     */
    private function readRemote(VideoAgentVideo $video): array
    {
        $id = (string) $video->heygen_id;

        return match ($video->kind) {
            VideoAgentKindEnum::Translation,
            VideoAgentKindEnum::Proofread => $this->client->getVideoTranslation($id),
            default                       => $this->client->getVideo($id),
        };
    }

    /**
     * Refresh one rendered video and hand it to the download job once ready.
     */
    public function syncVideo(VideoAgentVideo $video): VideoAgentVideo
    {
        if (blank($video->heygen_id) || $video->status->isTerminal() || $video->isReservedForApproval()) {
            return $video;
        }

        $response = $this->readRemote($video);

        $video->forceFill([
            'poll_attempts'  => $video->poll_attempts + 1,
            'last_polled_at' => now(),
        ])->save();

        if (isset($response['error'])) {
            return $video;
        }

        $data = $response['data'] ?? $response;
        $status = VideoStatusEnum::fromApi($data['status'] ?? null);

        $video->forceFill([
            'status'           => $status,
            'duration_seconds' => isset($data['duration']) ? (int) ceil((float) $data['duration']) : $video->duration_seconds,
            'remote_url'       => $data['video_url'] ?? $data['url'] ?? $video->remote_url,
            'thumbnail_url'    => $data['thumbnail_url'] ?? $video->thumbnail_url,
            'heygen_page_url'  => $data['video_page_url'] ?? $video->heygen_page_url,
            'heygen_share_url' => $data['video_share_page_url'] ?? $video->heygen_share_url,
            'payload'          => $data,
            'error_message'    => $status === VideoStatusEnum::Failed
                ? ($data['error']['message'] ?? $data['error'] ?? __('HeyGen could not render this video.'))
                : null,
        ])->save();

        if ($status === VideoStatusEnum::Completed) {
            DownloadVideoAgentOutputJob::dispatch($video->getKey());
        }

        return $video->refresh();
    }

    /**
     * Refresh many renders with a single bulk-status call, then only re-read
     * the ones HeyGen reports as finished. Falls back to one-by-one when the
     * bulk endpoint is unavailable.
     *
     * @param  Collection<int, VideoAgentVideo>  $videos
     */
    public function syncVideosInBulk(Collection $videos): int
    {
        $videos = $videos->filter(fn (VideoAgentVideo $video): bool => filled($video->heygen_id) && ! $video->isReservedForApproval());

        // The bulk endpoint only answers for video ids. Translation and
        // proofread rows carry a translation id, so they are read one by one.
        [$translations, $videos] = $videos->partition(
            fn (VideoAgentVideo $video): bool => in_array($video->kind, [
                VideoAgentKindEnum::Translation,
                VideoAgentKindEnum::Proofread,
            ], true),
        );

        $translations->each(fn (VideoAgentVideo $video) => $this->syncVideo($video));

        $handled = $translations->count();

        if ($videos->isEmpty()) {
            return $handled;
        }

        if ($videos->count() === 1) {
            $this->syncVideo($videos->first());

            return $handled + 1;
        }

        $response = $this->client->bulkVideoStatuses($videos->pluck('heygen_id')->all());

        if (isset($response['error'])) {
            $videos->each(fn (VideoAgentVideo $video) => $this->syncVideo($video));

            return $handled + $videos->count();
        }

        $statuses = collect($response['data'] ?? $response)
            ->mapWithKeys(fn ($entry): array => is_array($entry)
                ? [(string) ($entry['video_id'] ?? '') => (string) ($entry['status'] ?? '')]
                : []);

        $touched = $handled;

        foreach ($videos as $video) {
            $status = VideoStatusEnum::fromApi($statuses->get((string) $video->heygen_id));

            if (! $status->isTerminal()) {
                $video->forceFill(['poll_attempts' => $video->poll_attempts + 1, 'last_polled_at' => now()])->save();

                continue;
            }

            // Terminal: re-read the full record so we pick up the duration and
            // the presigned download URL.
            $this->syncVideo($video);
            $touched++;
        }

        return $touched;
    }

    public function callbackUrl(): ?string
    {
        if (! app()->routesAreCached() && ! app('router')->has('video-agent.webhook')) {
            return null;
        }

        $url = route('video-agent.webhook');

        // HeyGen only accepts publicly reachable HTTPS endpoints, so on a local
        // install we skip the callback and lean on the polling fallback.
        return str_starts_with($url, 'https://') ? $url : null;
    }

    /**
     * @param  array<int, array<string, mixed>>  $messages
     */
    private function persistMessages(VideoAgentSession $session, array $messages): void
    {
        $session->getConnection()->transaction(function () use ($session, $messages): void {
            $session->newQuery()->whereKey($session->getKey())->lockForUpdate()->first();

            $occurrences = [];

            foreach (array_reverse($messages) as $message) {
                if (! is_array($message)) {
                    continue;
                }

                $body = $message['content'] ?? $message['message'] ?? $message['text'] ?? null;

                if (blank($body)) {
                    continue;
                }

                $body = is_array($body) ? json_encode($body) : (string) $body;
                $role = ($message['role'] ?? 'agent') === 'user'
                    ? VideoAgentMessage::ROLE_USER
                    : VideoAgentMessage::ROLE_AGENT;
                $fingerprint = hash('sha256', json_encode([
                    $role,
                    $message['type'] ?? null,
                    $body,
                    $message['created_at'] ?? null,
                    $message['resource_ids'] ?? null,
                ], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));
                $occurrences[$fingerprint] = ($occurrences[$fingerprint] ?? 0) + 1;
                $heygenMessageId = (string) ($message['id'] ?? $message['message_id']
                    ?? 'fp-' . $fingerprint . '-' . $occurrences[$fingerprint]);
                $remoteCreatedAt = is_numeric($message['created_at'] ?? null)
                    ? Carbon::createFromTimestamp((int) $message['created_at'], config('app.timezone', 'UTC'))
                    : null;

                $persistedMessage = $session->messages()
                    ->where('heygen_message_id', $heygenMessageId)
                    ->first();

                if ($role === VideoAgentMessage::ROLE_USER && self::isOpeningPromptEcho(
                    $session,
                    $body,
                    is_numeric($message['created_at'] ?? null) ? (int) $message['created_at'] : null,
                )) {
                    $openingMessage = $session->messages()
                        ->where('role', VideoAgentMessage::ROLE_USER)
                        ->where('body', $session->prompt)
                        ->oldest('id')
                        ->first();

                    if ($openingMessage !== null && (
                        $openingMessage->heygen_message_id === null
                        || $openingMessage->heygen_message_id === $heygenMessageId
                        || str_starts_with($openingMessage->heygen_message_id, 'idx-')
                    )) {
                        if ($persistedMessage !== null && $persistedMessage->getKey() !== $openingMessage->getKey()) {
                            $persistedMessage->delete();
                        }

                        $session->messages()
                            ->where('role', VideoAgentMessage::ROLE_USER)
                            ->where('body', $body)
                            ->whereKeyNot($openingMessage->getKey())
                            ->get()
                            ->filter(fn (VideoAgentMessage $existing): bool => ($existing->meta['created_at'] ?? null) === ($message['created_at'] ?? null))
                            ->each->delete();

                        $openingMessage->forceFill([
                            'heygen_message_id' => $heygenMessageId,
                            'resource_id'       => $message['resource_id'] ?? ($message['resource_ids'][0] ?? null),
                            'meta'              => $message,
                        ])->save();

                        continue;
                    }
                }

                if ($persistedMessage === null && $role === VideoAgentMessage::ROLE_USER) {
                    $persistedMessage = $session->messages()
                        ->whereNull('heygen_message_id')
                        ->where('role', VideoAgentMessage::ROLE_USER)
                        ->where('body', $body)
                        ->oldest('id')
                        ->first();
                }

                if ($persistedMessage === null) {
                    $persistedMessage = $session->messages()
                        ->where('role', $role)
                        ->where('body', $body)
                        ->where('heygen_message_id', 'like', 'idx-%')
                        ->get()
                        ->first(fn (VideoAgentMessage $existing): bool => ($existing->meta['created_at'] ?? null) === ($message['created_at'] ?? null));
                }

                if ($persistedMessage === null) {
                    $session->messages()->forceCreate([
                        'heygen_message_id' => $heygenMessageId,
                        'role'              => $role,
                        'kind'              => VideoAgentMessage::KIND_MESSAGE,
                        'body'              => $body,
                        'resource_id'       => $message['resource_id'] ?? ($message['resource_ids'][0] ?? null),
                        'meta'              => $message,
                        'created_at'        => $remoteCreatedAt ?? now(),
                    ]);

                    continue;
                }

                $persistedMessage->forceFill([
                    'heygen_message_id' => $heygenMessageId,
                    'role'              => $role,
                    'body'              => $body,
                    'resource_id'       => $message['resource_id'] ?? ($message['resource_ids'][0] ?? null),
                    'meta'              => $message,
                    'created_at'        => $remoteCreatedAt ?? $persistedMessage->created_at,
                ])->save();
            }
        });
    }

    private function attachVideoIfPresent(VideoAgentSession $session, ?string $heygenVideoId): ?VideoAgentVideo
    {
        if (blank($heygenVideoId)) {
            return null;
        }

        $video = VideoAgentVideo::query()->updateOrCreate(
            ['heygen_id' => $heygenVideoId],
            [
                'user_id'    => $session->user_id,
                'team_id'    => $session->team_id,
                'session_id' => $session->getKey(),
                'kind'       => VideoAgentKindEnum::Agent,
                'title'      => $session->displayTitle(),
            ],
        );

        if ($video->wasRecentlyCreated && ! $session->status->needsUser()) {
            RefreshVideoAgentStatusJob::dispatch($session->getKey());
        }

        return $video;
    }
}
