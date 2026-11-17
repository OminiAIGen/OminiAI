<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Services\HeyGen\Concerns;

/**
 * A character is an avatar *group*; each group holds one or more *looks*
 * (outfits, poses, styles). The look id is what becomes `avatar_id` on a video
 * or a Video Agent session, so looks are what the picker syncs.
 *
 * Groups live at `/v3/avatars`, not `/v3/avatars/groups` — the plural-noun form
 * 404s. Only looks carry the extra `/looks` segment.
 */
trait HasAvatars
{
    /**
     * HeyGen answers 400 `invalid_parameter` above this on both avatar
     * endpoints. Every other catalog endpoint takes the shared page size of
     * 100, so the ceiling is applied here rather than slowing the whole sync.
     */
    private const MAX_PAGE_SIZE = 50;

    /**
     * A "look" id is what you pass as `avatar_id` when creating a video.
     *
     * @param  array{group_id?: string, avatar_type?: string, ownership?: string}  $filters
     *
     * @return array<int, array<string, mixed>>
     */
    public function listAvatarLooks(array $filters = [], int $limit = self::MAX_PAGE_SIZE): array
    {
        return $this->paginate('/v3/avatars/looks', $filters + ['limit' => $this->avatarPageSize($limit)]);
    }

    /**
     * @param  callable(array<int, array<string, mixed>>): void  $onPage
     * @param  array{group_id?: string, avatar_type?: string, ownership?: string}  $filters
     */
    public function eachAvatarLookPage(callable $onPage, array $filters = [], int $limit = self::MAX_PAGE_SIZE, int $maxPages = 20): bool
    {
        return $this->paginateEach(
            '/v3/avatars/looks',
            $filters + ['limit' => $this->avatarPageSize($limit)],
            $onPage,
            $maxPages,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function getAvatarLook(string $lookId): array
    {
        return $this->get("/v3/avatars/looks/{$lookId}");
    }

    /**
     * Renames a look. Supported for photo avatars and digital twins only.
     *
     * @param  array<string, mixed>  $payload
     *
     * @return array<string, mixed>
     */
    public function updateAvatarLook(string $lookId, array $payload): array
    {
        return $this->patch("/v3/avatars/looks/{$lookId}", $payload);
    }

    /**
     * Deleting the last look in a group deletes the parent group too. Studio
     * avatar looks cannot be deleted over the API.
     *
     * @return array<string, mixed>
     */
    public function deleteAvatarLook(string $lookId): array
    {
        return $this->delete("/v3/avatars/looks/{$lookId}");
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listAvatarGroups(int $limit = self::MAX_PAGE_SIZE): array
    {
        return $this->paginate('/v3/avatars', ['limit' => $this->avatarPageSize($limit)]);
    }

    /**
     * @return array<string, mixed>
     */
    public function getAvatarGroup(string $groupId): array
    {
        return $this->get("/v3/avatars/{$groupId}");
    }

    /**
     * Permanent. Public and community groups cannot be deleted.
     *
     * @return array<string, mixed>
     */
    public function deleteAvatarGroup(string $groupId): array
    {
        return $this->delete("/v3/avatars/{$groupId}");
    }

    /**
     * @param  array<string, mixed>  $payload
     *
     * @return array<string, mixed>
     */
    public function createAvatar(array $payload): array
    {
        return $this->post('/v3/avatars', $payload);
    }

    /**
     * Consent is granted against the group, not a single look: it is the person
     * who agreed to be cloned, and every look of theirs inherits it.
     *
     * @param  array<string, mixed>  $payload
     *
     * @return array<string, mixed>
     */
    public function createAvatarConsent(string $groupId, array $payload = []): array
    {
        return $this->post("/v3/avatars/{$groupId}/consent", $payload);
    }

    private function avatarPageSize(int $limit): int
    {
        return max(1, min($limit, self::MAX_PAGE_SIZE));
    }
}
