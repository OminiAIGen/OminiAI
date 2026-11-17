<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Services;

use App\Extensions\VideoAgent\System\Jobs\SyncHeyGenCatalogJob;
use App\Extensions\VideoAgent\System\Models\VideoAgentAvatar;
use App\Extensions\VideoAgent\System\Models\VideoAgentStyle;
use App\Extensions\VideoAgent\System\Models\VideoAgentVoice;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * Decides when the local HeyGen catalog mirror needs refreshing and keeps that
 * work off the request thread.
 *
 * The catalog endpoints are cursor paginated and return thousands of rows, so
 * walking them inside a controller costs 20 sequential HTTP round trips. Pages
 * therefore always render from the local tables and only queue a sync.
 */
class CatalogSyncService
{
    public const SECTIONS = ['styles', 'avatars', 'voices'];

    /**
     * Queue a sync for every section that has never been synced or has gone
     * stale. A section that came back empty still counts as synced, so an
     * account without custom avatars does not re-hit HeyGen on every page view.
     *
     * @param  array<int, string>  $sections
     */
    public function ensure(array $sections): void
    {
        $due = array_values(array_filter($sections, fn (string $section): bool => $this->isDue($section)));

        if ($due === []) {
            return;
        }

        $this->dispatch($due);
    }

    /**
     * Force a refresh, ignoring the TTL. Returns true when the work was handed
     * to a queue worker, false when it had to run inline.
     *
     * @param  array<int, string>  $sections
     */
    public function refresh(array $sections): bool
    {
        foreach ($sections as $section) {
            Cache::forget($this->syncedKey($section));
        }

        return $this->dispatch($sections);
    }

    /**
     * @param  array<int, string>  $sections
     */
    public function isSyncing(array $sections): bool
    {
        foreach ($sections as $section) {
            if (Cache::get($this->runningKey($section)) === true) {
                return true;
            }
        }

        return false;
    }

    public function isDue(string $section): bool
    {
        if (Cache::has($this->syncedKey($section)) || $this->isSyncing([$section])) {
            return false;
        }

        $lastRow = $this->lastRowSyncedAt($section);

        if ($lastRow !== null && $lastRow->greaterThan(now()->subMinutes($this->ttlMinutes()))) {
            Cache::put($this->syncedKey($section), $lastRow->timestamp, $lastRow->copy()->addMinutes($this->ttlMinutes()));

            return false;
        }

        return true;
    }

    /**
     * Survives a cache flush: the mirrored rows themselves record when they
     * were last written, so a warm catalog is not re-fetched just because the
     * cache is cold.
     */
    private function lastRowSyncedAt(string $section): ?Carbon
    {
        $model = match ($section) {
            'styles'  => VideoAgentStyle::class,
            'avatars' => VideoAgentAvatar::class,
            'voices'  => VideoAgentVoice::class,
            default   => null,
        };

        if ($model === null) {
            return null;
        }

        $latest = $model::query()->max('synced_at');

        return blank($latest) ? null : Carbon::parse($latest);
    }

    private function ttlMinutes(): int
    {
        return (int) config('video-agent.catalog.ttl_minutes', 720);
    }

    public function lastSyncedAt(string $section): ?Carbon
    {
        $timestamp = Cache::get($this->syncedKey($section));

        return is_int($timestamp) ? Carbon::createFromTimestamp($timestamp) : null;
    }

    public function markRunning(string $section): void
    {
        Cache::put($this->runningKey($section), true, (int) config('video-agent.catalog.running_ttl', 900));
    }

    public function markFinished(string $section): void
    {
        Cache::forget($this->runningKey($section));
        Cache::put(
            $this->syncedKey($section),
            now()->timestamp,
            now()->addMinutes((int) config('video-agent.catalog.ttl_minutes', 720)),
        );
    }

    /**
     * Clears the "in progress" flag without recording a successful sync, so the
     * next page view tries again instead of waiting out the TTL.
     */
    public function markFailed(string $section): void
    {
        Cache::forget($this->runningKey($section));
    }

    /**
     * @param  array<int, string>  $sections
     */
    private function dispatch(array $sections): bool
    {
        foreach ($sections as $section) {
            $this->markRunning($section);
        }

        if (config('queue.default') === 'sync') {
            SyncHeyGenCatalogJob::dispatchSync($sections);

            return false;
        }

        SyncHeyGenCatalogJob::dispatch($sections);

        return true;
    }

    private function runningKey(string $section): string
    {
        return "video_agent_catalog_running_{$section}";
    }

    private function syncedKey(string $section): string
    {
        return "video_agent_catalog_synced_{$section}";
    }
}
