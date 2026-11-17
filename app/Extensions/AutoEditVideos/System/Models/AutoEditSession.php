<?php

declare(strict_types=1);

namespace App\Extensions\AutoEditVideos\System\Models;

use App\Extensions\AutoEditVideos\System\Enums\SessionStatusEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class AutoEditSession extends Model
{
    protected $table = 'ext_auto_edit_videos_sessions';

    protected $fillable = [
        'uuid',
        'user_id',
        'team_id',
        'rendley_project_id',
        'rendley_thread_id',
        'rendley_job_id',
        'prompt',
        'title',
        'instructions',
        'duration_seconds',
        'target_resolution',
        'aspect_ratio',
        'status',
        'project_url',
        'error_message',
        'poll_attempts',
        'last_polled_at',
        'meta',
    ];

    /**
     * A database default is not applied to the in-memory model after a create,
     * so `status` would read as null until the row was refreshed.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status'        => 'planning',
        'poll_attempts' => 0,
    ];

    protected $casts = [
        'status'           => SessionStatusEnum::class,
        'duration_seconds' => 'integer',
        'poll_attempts'    => 'integer',
        'last_polled_at'   => 'datetime',
        'meta'             => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(static function (self $session): void {
            if (blank($session->uuid)) {
                $session->uuid = (string) Str::uuid();
            }
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
        return $this->hasMany(AutoEditMessage::class, 'session_id')->orderBy('created_at');
    }

    public function sources(): HasMany
    {
        return $this->hasMany(AutoEditSource::class, 'session_id');
    }

    public function renders(): HasMany
    {
        return $this->hasMany(AutoEditRender::class, 'session_id')->latest('id');
    }

    public function latestRender(): ?AutoEditRender
    {
        return $this->renders()->first();
    }

    public function isTerminal(): bool
    {
        return $this->status->isTerminal();
    }

    /**
     * Sessions the poll sweep should still ask Rendley about.
     */
    public function scopeAwaitingAgent(Builder $query): Builder
    {
        return $query->where('status', SessionStatusEnum::Planning->value);
    }

    public function displayTitle(): string
    {
        return $this->title ?: Str::limit($this->prompt, 60);
    }
}
