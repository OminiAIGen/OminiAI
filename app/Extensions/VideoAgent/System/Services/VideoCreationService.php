<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Services;

use App\Extensions\VideoAgent\System\Enums\VideoAgentKindEnum;
use App\Extensions\VideoAgent\System\Enums\VideoStatusEnum;
use App\Extensions\VideoAgent\System\Jobs\RefreshVideoAgentVideoJob;
use App\Extensions\VideoAgent\System\Models\VideoAgentVideo;
use App\Extensions\VideoAgent\System\Services\HeyGen\HeyGenClient;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Direct generation through `POST /v3/videos`. The endpoint is a discriminated
 * union on `type`, so each mode builds its own body but they all share the
 * output, caption and background options.
 */
class VideoCreationService
{
    public const TYPE_AVATAR = 'avatar';

    public const TYPE_IMAGE = 'image';

    public const TYPE_CINEMATIC = 'cinematic_avatar';

    public const TYPE_STUDIO = 'studio';

    public function __construct(
        private readonly HeyGenClient $client,
        private readonly VideoAgentBillingService $billing,
    ) {}

    /**
     * @param  array<string, mixed>  $input
     * @return VideoAgentVideo|string the render, or the reason it was refused
     */
    public function create(User $user, array $input): VideoAgentVideo|string
    {
        $type = (string) ($input['type'] ?? self::TYPE_AVATAR);
        $kind = $this->kindFor($type);
        $estimate = $this->estimateSeconds($type, $input);

        $allowed = $this->billing->assertCanStart($user, $kind, $estimate);

        if ($allowed !== true) {
            return $allowed;
        }

        $payload = $this->buildPayload($type, $input);

        $video = VideoAgentVideo::query()->create([
            'user_id' => $user->id,
            'team_id' => $user->team_id ?? null,
            'kind'    => $kind,
            'title'   => $input['title'] ?? $this->titleFor($type, $input),
            'status'  => VideoStatusEnum::Queued,
            'payload' => $payload,
        ]);

        $response = $this->client->createVideo($payload);

        if (isset($response['error'])) {
            $video->forceFill([
                'status'        => VideoStatusEnum::Failed,
                'error_message' => $response['message'] ?? __('HeyGen rejected the request.'),
            ])->save();

            return (string) ($response['message'] ?? __('HeyGen rejected the request.'));
        }

        $data = $response['data'] ?? $response;

        $video->forceFill([
            'heygen_id' => $data['video_id'] ?? Arr::get($data, 'id'),
            'status'    => VideoStatusEnum::fromApi($data['status'] ?? null),
        ])->save();

        RefreshVideoAgentVideoJob::dispatch($video->getKey());

        return $video->refresh();
    }

    public function kindFor(string $type): VideoAgentKindEnum
    {
        return match ($type) {
            self::TYPE_IMAGE     => VideoAgentKindEnum::Image,
            self::TYPE_CINEMATIC => VideoAgentKindEnum::Cinematic,
            self::TYPE_STUDIO    => VideoAgentKindEnum::Studio,
            default              => VideoAgentKindEnum::Avatar,
        };
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<string, mixed>
     */
    private function buildPayload(string $type, array $input): array
    {
        $shared = array_filter([
            'resolution'   => $input['resolution'] ?? setting('video_agent_default_resolution', config('video-agent.defaults.resolution')),
            'aspect_ratio' => $input['aspect_ratio'] ?? config('video-agent.defaults.aspect_ratio'),
            'callback_id'  => (string) Str::uuid(),
        ], static fn ($value) => $value !== null && $value !== '');

        return match ($type) {
            self::TYPE_IMAGE     => $shared + array_filter([
                'type'          => self::TYPE_IMAGE,
                'image'         => $this->imageSource($input),
                'script'        => $input['script'] ?? null,
                'voice_id'      => $input['voice_id'] ?? null,
                'motion_prompt' => $input['motion_prompt'] ?? null,
            ], static fn ($value) => $value !== null && $value !== ''),

            self::TYPE_CINEMATIC => $shared + array_filter([
                'type'           => self::TYPE_CINEMATIC,
                'prompt'         => $input['prompt'] ?? null,
                'avatar_id'      => array_slice((array) ($input['avatar_ids'] ?? []), 0, 3),
                'duration'       => isset($input['duration']) ? (int) $input['duration'] : null,
                'auto_duration'  => ! isset($input['duration']),
                'enhance_prompt' => (bool) ($input['enhance_prompt'] ?? false),
            ], static fn ($value) => $value !== null && $value !== '' && $value !== []),

            self::TYPE_STUDIO    => $shared + [
                'type'   => self::TYPE_STUDIO,
                'scenes' => $this->scenes($input),
            ],

            default              => $shared + array_filter([
                'type'              => self::TYPE_AVATAR,
                'avatar_id'         => $input['avatar_id'] ?? null,
                'script'            => $input['script'] ?? null,
                'voice_id'          => $input['voice_id'] ?? null,
                'engine'            => $input['engine'] ?? setting('video_agent_default_engine', config('video-agent.defaults.engine')),
                'motion_prompt'     => $input['motion_prompt'] ?? null,
                'remove_background' => (bool) ($input['remove_background'] ?? false),
            ], static fn ($value) => $value !== null && $value !== ''),
        };
    }

    /**
     * `image` accepts a URL or a previously uploaded asset id.
     *
     * @param  array<string, mixed>  $input
     * @return array<string, string>
     */
    private function imageSource(array $input): array
    {
        if (filled($input['image_asset_id'] ?? null)) {
            return ['type' => 'asset_id', 'asset_id' => (string) $input['image_asset_id']];
        }

        return ['type' => 'url', 'url' => (string) ($input['image_url'] ?? '')];
    }

    /**
     * @param  array<string, mixed>  $input
     * @return array<int, array<string, mixed>>
     */
    private function scenes(array $input): array
    {
        return collect($input['scenes'] ?? [])
            ->filter(fn ($scene): bool => is_array($scene))
            ->map(fn (array $scene): array => array_filter([
                'type'      => $scene['type'] ?? 'avatar_video',
                'avatar_id' => $scene['avatar_id'] ?? null,
                'voice_id'  => $scene['voice_id'] ?? null,
                'script'    => $scene['script'] ?? null,
                'image'     => filled($scene['image_url'] ?? null)
                    ? ['type' => 'url', 'url' => $scene['image_url']]
                    : null,
            ], static fn ($value) => $value !== null && $value !== ''))
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $input
     */
    private function estimateSeconds(string $type, array $input): int
    {
        if ($type === self::TYPE_CINEMATIC) {
            return (int) ($input['duration'] ?? 15);
        }

        if ($type === self::TYPE_STUDIO) {
            $scripts = collect($input['scenes'] ?? [])->pluck('script')->filter()->implode(' ');

            return $this->billing->estimateSecondsFromPrompt($scripts ?: 'scene');
        }

        return $this->billing->estimateSecondsFromPrompt((string) ($input['script'] ?? $input['prompt'] ?? ''));
    }

    /**
     * @param  array<string, mixed>  $input
     */
    private function titleFor(string $type, array $input): string
    {
        $source = $input['script'] ?? $input['prompt'] ?? '';

        return filled($source)
            ? Str::limit((string) $source, 80)
            : $this->kindFor($type)->label();
    }
}
