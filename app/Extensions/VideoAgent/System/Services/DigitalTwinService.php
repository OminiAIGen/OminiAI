<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Services;

use App\Extensions\VideoAgent\System\Models\VideoAgentAvatar;
use App\Extensions\VideoAgent\System\Services\HeyGen\HeyGenClient;

class DigitalTwinService
{
    public function __construct(private readonly HeyGenClient $client) {}

    public function create(int $userId, string $name, string $assetId): ?VideoAgentAvatar
    {
        $response = $this->client->createAvatar([
            'type' => 'digital_twin',
            'name' => $name,
            'file' => ['type' => 'asset_id', 'asset_id' => $assetId],
        ]);

        if (isset($response['error'])) {
            return null;
        }

        $look = data_get($response, 'data.avatar_item');
        $group = data_get($response, 'data.avatar_group');

        if (! is_array($look) || ! is_array($group) || blank($look['id'] ?? null) || blank($group['id'] ?? null)) {
            return null;
        }

        $avatar = VideoAgentAvatar::query()->firstOrNew(['heygen_id' => (string) $look['id']]);

        if ($avatar->exists && $avatar->user_id !== $userId) {
            return null;
        }

        $avatar->fill([
            'user_id'               => $userId,
            'group_id'              => (string) $group['id'],
            'name'                  => $name,
            'avatar_type'           => 'digital_twin',
            'ownership'             => 'private',
            'status'                => $look['status'] ?? 'processing',
            'consent_status'        => $group['consent_status'] ?? 'pending',
            'preview_image_url'     => $look['preview_image_url'] ?? null,
            'preview_video_url'     => $look['preview_video_url'] ?? null,
            'default_voice_id'      => $look['default_voice_id'] ?? null,
            'supported_api_engines' => $look['supported_api_engines'] ?? [],
            'payload'               => $look,
            'synced_at'             => now(),
        ])->save();

        return $avatar;
    }

    public function consentUrl(VideoAgentAvatar $avatar): ?string
    {
        if ($avatar->avatar_type !== 'digital_twin' || $avatar->status !== 'completed' || blank($avatar->group_id)) {
            return null;
        }

        $response = $this->client->createAvatarConsent((string) $avatar->group_id);

        if (isset($response['error'])) {
            return null;
        }

        $status = data_get($response, 'data.avatar_group.consent_status');

        if (is_string($status)) {
            $avatar->forceFill(['consent_status' => $status])->save();
        }

        $url = data_get($response, 'data.url');

        return is_string($url) && str_starts_with($url, 'https://') ? $url : null;
    }

    public function refresh(VideoAgentAvatar $avatar): VideoAgentAvatar
    {
        $lookResponse = $this->client->getAvatarLook((string) $avatar->heygen_id);
        $groupResponse = filled($avatar->group_id)
            ? $this->client->getAvatarGroup((string) $avatar->group_id)
            : [];

        $look = data_get($lookResponse, 'data.avatar_item', $lookResponse['data'] ?? null);
        $group = data_get($groupResponse, 'data.avatar_group', $groupResponse['data'] ?? null);
        $changes = ['last_polled_at' => now()];

        if (! isset($lookResponse['error']) && is_array($look)) {
            $changes['status'] = $look['status'] ?? $avatar->status;
            $changes['preview_image_url'] = $look['preview_image_url'] ?? $avatar->preview_image_url;
            $changes['preview_video_url'] = $look['preview_video_url'] ?? $avatar->preview_video_url;
            $changes['default_voice_id'] = $look['default_voice_id'] ?? $avatar->default_voice_id;
            $changes['payload'] = $look;
            $changes['synced_at'] = now();
        }

        if (! isset($groupResponse['error']) && is_array($group)) {
            $changes['consent_status'] = $group['consent_status'] ?? $avatar->consent_status;
        }

        $avatar->forceFill($changes)->save();

        return $avatar->refresh();
    }
}
