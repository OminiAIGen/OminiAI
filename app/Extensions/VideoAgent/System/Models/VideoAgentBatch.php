<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class VideoAgentBatch extends Model
{
    public const KIND_VIDEO = 'video';

    public const KIND_LIPSYNC = 'lipsync';

    public const KIND_TRANSLATION = 'translation';

    protected $table = 'ext_video_agent_batches';

    protected $fillable = [
        'uuid',
        'user_id',
        'heygen_id',
        'kind',
        'status',
        'item_count',
        'completed_count',
        'error_message',
        'payload',
        'last_polled_at',
    ];

    protected $casts = [
        'item_count'      => 'integer',
        'completed_count' => 'integer',
        'payload'         => 'array',
        'last_polled_at'  => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $batch): void {
            $batch->uuid ??= (string) Str::uuid();
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnfinished(Builder $query): Builder
    {
        return $query->whereNotIn('status', ['completed', 'failed']);
    }

    public function progressPercent(): int
    {
        if ($this->item_count <= 0) {
            return 0;
        }

        return (int) min(100, round($this->completed_count / $this->item_count * 100));
    }
}
