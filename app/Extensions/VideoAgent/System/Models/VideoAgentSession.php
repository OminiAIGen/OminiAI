<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Models;

use App\Extensions\VideoAgent\System\Enums\SessionModeEnum;
use App\Extensions\VideoAgent\System\Enums\SessionStatusEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property SessionStatusEnum $status
 * @property SessionModeEnum $mode
 */
class VideoAgentSession extends Model
{
    protected $table = 'ext_video_agent_sessions';

    protected $fillable = [
        'uuid',
        'user_id',
        'team_id',
        'heygen_session_id',
        'mode',
        'prompt',
        'title',
        'style_id',
        'brand_kit_id',
        'avatar_id',
        'voice_id',
        'orientation',
        'duration_seconds',
        'instructions',
        'resolution',
        'status',
        'error_message',
        'poll_attempts',
        'last_polled_at',
        'meta',
    ];

    protected $casts = [
        'mode'             => SessionModeEnum::class,
        'status'           => SessionStatusEnum::class,
        'duration_seconds' => 'integer',
        'poll_attempts'    => 'integer',
        'last_polled_at'   => 'datetime',
        'meta'             => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $session): void {
            $session->uuid ??= (string) Str::uuid();
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

    public function messages(): HasMany
    {
        return $this->hasMany(VideoAgentMessage::class, 'session_id');
    }

    public function resources(): HasMany
    {
        return $this->hasMany(VideoAgentResource::class, 'session_id');
    }

    public function videos(): HasMany
    {
        return $this->hasMany(VideoAgentVideo::class, 'session_id');
    }

    public function storyboard(): ?VideoAgentResource
    {
        return $this->resources()->where('type', 'storyboard')->latest('fetched_at')->first();
    }

    public function scopeUnfinished(Builder $query): Builder
    {
        return $query->whereNotIn('status', [
            SessionStatusEnum::Completed->value,
            SessionStatusEnum::Failed->value,
            SessionStatusEnum::Stopped->value,
        ]);
    }

    public function scopeAwaitingHeyGen(Builder $query): Builder
    {
        return $query->unfinished()->whereNotIn('status', [
            SessionStatusEnum::WaitingForInput->value,
            SessionStatusEnum::Reviewing->value,
        ]);
    }

    public function isTerminal(): bool
    {
        return $this->status->isTerminal();
    }

    public function displayTitle(): string
    {
        return $this->title ?: Str::limit($this->prompt, 60);
    }
}
