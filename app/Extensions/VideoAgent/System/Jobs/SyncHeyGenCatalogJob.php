<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Jobs;

use App\Extensions\VideoAgent\System\Models\VideoAgentAvatar;
use App\Extensions\VideoAgent\System\Models\VideoAgentStyle;
use App\Extensions\VideoAgent\System\Models\VideoAgentVoice;
use App\Extensions\VideoAgent\System\Services\CatalogSyncService;
use App\Extensions\VideoAgent\System\Services\HeyGen\HeyGenClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

/**
 * Caches the HeyGen catalog (Look Packs, avatar looks, voices) locally so the
 * pickers render without a round trip on every page view.
 *
 * The catalog runs to thousands of rows, so each API page is written with a
 * single bulk upsert rather than a per-row updateOrCreate.
 */
class SyncHeyGenCatalogJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $timeout = 600;

    public int $tries = 2;

    public int $uniqueFor = 900;

    /**
     * @param  array<int, string>  $only  styles|avatars|voices, empty means all
     */
    public function __construct(public array $only = []) {}

    public function uniqueId(): string
    {
        return implode('-', $this->only) ?: 'all';
    }

    public function handle(HeyGenClient $client, CatalogSyncService $catalog): void
    {
        foreach (CatalogSyncService::SECTIONS as $section) {
            if (! $this->wants($section)) {
                continue;
            }

            if (! $client->isConfigured()) {
                $catalog->markFailed($section);

                continue;
            }

            $catalog->markRunning($section);

            try {
                $completed = match ($section) {
                    'styles'  => $this->syncStyles($client),
                    'avatars' => $this->syncAvatars($client),
                    'voices'  => $this->syncVoices($client),
                };

                // A walk cut short by an API error must not look fresh, or the
                // TTL would lock in a half-written catalog.
                $completed
                    ? $catalog->markFinished($section)
                    : $catalog->markFailed($section);
            } catch (Throwable $e) {
                $catalog->markFailed($section);

                throw $e;
            }
        }
    }

    public function failed(?Throwable $exception): void
    {
        $catalog = app(CatalogSyncService::class);

        foreach (CatalogSyncService::SECTIONS as $section) {
            if ($this->wants($section)) {
                $catalog->markFailed($section);
            }
        }
    }

    private function wants(string $section): bool
    {
        return $this->only === [] || in_array($section, $this->only, true);
    }

    private function syncStyles(HeyGenClient $client): bool
    {
        $completed = $client->eachVideoAgentStylePage(
            fn (array $page) => $this->upsert(new VideoAgentStyle, $page, fn (array $style): ?array => $this->styleRow($style)),
            $this->pageSize(),
            $this->maxPages(),
        );

        VideoAgentStyle::forgetFilterCache();

        return $completed;
    }

    private function syncAvatars(HeyGenClient $client): bool
    {
        return $client->eachAvatarLookPage(
            function (array $page): void {
                $existing = VideoAgentAvatar::query()
                    ->whereIn('heygen_id', array_filter(array_map(static fn (array $look): mixed => $look['id'] ?? $look['avatar_id'] ?? null, $page)))
                    ->get(['heygen_id', 'user_id', 'ownership'])
                    ->keyBy('heygen_id');

                $this->upsert(new VideoAgentAvatar, $page, fn (array $look): ?array => $this->avatarRow(
                    $look,
                    $existing->get((string) ($look['id'] ?? $look['avatar_id'] ?? '')),
                ));
            },
            [],
            $this->pageSize(),
            $this->maxPages(),
        );
    }

    private function syncVoices(HeyGenClient $client): bool
    {
        $completed = $client->eachVoicePage(
            fn (array $page) => $this->upsert(new VideoAgentVoice, $page, fn (array $voice): ?array => $this->voiceRow($voice)),
            [],
            $this->pageSize(),
            $this->maxPages(),
        );

        VideoAgentVoice::forgetFilterCache();

        return $completed;
    }

    /**
     * One INSERT ... ON DUPLICATE KEY UPDATE per API page instead of two
     * queries per row. Values bypass Eloquent casts, so JSON and booleans are
     * prepared by the row builders.
     *
     * @param  array<int, array<string, mixed>>  $items
     * @param  callable(array<string, mixed>): ?array<string, mixed>  $toRow
     */
    private function upsert(Model $model, array $items, callable $toRow): void
    {
        $rows = [];

        foreach ($items as $item) {
            $row = $toRow($item);

            if ($row !== null) {
                $rows[$row['heygen_id']] = $row;
            }
        }

        if ($rows === []) {
            return;
        }

        $rows = array_values($rows);

        $model->newQuery()->upsert(
            $rows,
            ['heygen_id'],
            array_values(array_diff(array_keys($rows[0]), ['heygen_id'])),
        );
    }

    /**
     * @param  array<string, mixed>  $style
     *
     * @return array<string, mixed>|null
     */
    private function styleRow(array $style): ?array
    {
        $id = $style['style_id'] ?? $style['id'] ?? null;

        if (blank($id)) {
            return null;
        }

        return [
            'heygen_id'         => (string) $id,
            'name'              => $style['name'] ?? null,
            'thumbnail_url'     => $style['thumbnail_url'] ?? null,
            'preview_video_url' => $style['preview_video_url'] ?? null,
            'aspect_ratio'      => $style['aspect_ratio'] ?? null,
            'tags'              => $this->json($style['tags'] ?? []),
            'payload'           => $this->json($style),
            'synced_at'         => now(),
        ];
    }

    /**
     * @param  array<string, mixed>  $look
     *
     * @return array<string, mixed>|null
     */
    private function avatarRow(array $look, ?VideoAgentAvatar $existing = null): ?array
    {
        $id = $look['id'] ?? $look['avatar_id'] ?? null;

        if (blank($id)) {
            return null;
        }

        return [
            'heygen_id'             => (string) $id,
            'group_id'              => $look['group_id'] ?? null,
            'name'                  => $look['name'] ?? null,
            'avatar_type'           => $look['avatar_type'] ?? null,
            'ownership'             => $existing?->user_id !== null
                ? 'private'
                : ($look['ownership'] ?? $existing?->ownership ?? 'private'),
            'gender'                => $look['gender'] ?? null,
            'status'                => $look['status'] ?? null,
            'preview_image_url'     => $look['preview_image_url'] ?? null,
            'preview_video_url'     => $look['preview_video_url'] ?? null,
            'default_voice_id'      => $look['default_voice_id'] ?? null,
            'preferred_orientation' => $look['preferred_orientation'] ?? null,
            'supported_api_engines' => $this->json($look['supported_api_engines'] ?? []),
            'tags'                  => $this->json($look['tags'] ?? []),
            'payload'               => $this->json($look),
            'synced_at'             => now(),
        ];
    }

    /**
     * @param  array<string, mixed>  $voice
     *
     * @return array<string, mixed>|null
     */
    private function voiceRow(array $voice): ?array
    {
        $id = $voice['voice_id'] ?? $voice['id'] ?? null;

        if (blank($id)) {
            return null;
        }

        return [
            'heygen_id'         => (string) $id,
            'name'              => $voice['name'] ?? null,
            'language'          => VideoAgentVoice::normalizeLanguage($voice['language'] ?? null),
            'gender'            => $voice['gender'] ?? null,
            'type'              => $voice['type'] ?? 'public',
            'preview_audio_url' => $voice['preview_audio_url'] ?? null,
            'support_pause'     => (int) (bool) ($voice['support_pause'] ?? false),
            'support_locale'    => (int) (bool) ($voice['support_locale'] ?? false),
            'payload'           => $this->json($voice),
            'synced_at'         => now(),
        ];
    }

    private function json(mixed $value): ?string
    {
        return $value === null ? null : json_encode($value);
    }

    private function pageSize(): int
    {
        return (int) config('video-agent.catalog.page_size', 100);
    }

    private function maxPages(): int
    {
        return (int) config('video-agent.catalog.max_pages', 20);
    }
}
