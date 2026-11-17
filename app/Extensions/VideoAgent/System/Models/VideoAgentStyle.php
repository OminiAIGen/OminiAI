<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class VideoAgentStyle extends Model
{
    private const TAGS_CACHE_KEY = 'video_agent_style_tags';

    protected $table = 'ext_video_agent_styles';

    protected $fillable = [
        'heygen_id',
        'name',
        'thumbnail_url',
        'preview_video_url',
        'aspect_ratio',
        'tags',
        'payload',
        'synced_at',
    ];

    protected $casts = [
        'tags'      => 'array',
        'payload'   => 'array',
        'synced_at' => 'datetime',
    ];

    public function scopeTagged(Builder $query, ?string $tag): Builder
    {
        if (blank($tag)) {
            return $query;
        }

        return $query->whereJsonContains('tags', $tag);
    }

    /**
     * Cached because building it pulls every style row into PHP just to render
     * the tag filter.
     *
     * @return array<int, string>
     */
    public static function availableTags(): array
    {
        return Cache::remember(
            self::TAGS_CACHE_KEY,
            now()->addMinutes((int) config('video-agent.catalog.filters_ttl', 720)),
            static fn (): array => static::query()
                ->pluck('tags')
                ->flatten()
                ->filter()
                ->unique()
                ->sort()
                ->values()
                ->all(),
        );
    }

    public static function forgetFilterCache(): void
    {
        Cache::forget(self::TAGS_CACHE_KEY);
    }
}
