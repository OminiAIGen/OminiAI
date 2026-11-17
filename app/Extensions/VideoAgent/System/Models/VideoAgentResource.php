<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoAgentResource extends Model
{
    public const TYPE_STORYBOARD = 'storyboard';

    protected $table = 'ext_video_agent_resources';

    protected $fillable = [
        'session_id',
        'heygen_resource_id',
        'type',
        'payload',
        'fetched_at',
    ];

    protected $casts = [
        'payload'    => 'array',
        'fetched_at' => 'datetime',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(VideoAgentSession::class, 'session_id');
    }

    /**
     * Normalised scene list for the storyboard viewer.
     *
     * @return array<int, array{index: int, title: ?string, narration: ?string, visual: ?string, on_screen_text: ?string, duration: ?float, thumbnail: ?string}>
     */
    public function scenes(): array
    {
        $payload = $this->payload ?? [];
        $raw = $payload['scenes'] ?? $payload['data']['scenes'] ?? $payload['storyboard']['scenes'] ?? [];

        if (! is_array($raw)) {
            return [];
        }

        return collect($raw)->values()->map(fn ($scene, int $index): array => [
            'index'          => $index + 1,
            'title'          => $scene['title'] ?? $scene['name'] ?? null,
            'narration'      => $scene['narration'] ?? $scene['script'] ?? $scene['voiceover'] ?? null,
            'visual'         => $scene['visual'] ?? $scene['description'] ?? $scene['visual_description'] ?? null,
            'on_screen_text' => $scene['on_screen_text'] ?? $scene['text'] ?? null,
            'duration'       => isset($scene['duration']) ? (float) $scene['duration'] : null,
            'thumbnail'      => $scene['thumbnail_url'] ?? $scene['image_url'] ?? null,
        ])->all();
    }

    public function estimatedSeconds(): int
    {
        $total = collect($this->scenes())->sum(fn (array $scene): float => (float) ($scene['duration'] ?? 0));

        return (int) ceil($total);
    }
}
