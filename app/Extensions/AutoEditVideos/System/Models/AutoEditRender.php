<?php

declare(strict_types=1);

namespace App\Extensions\AutoEditVideos\System\Models;

use App\Extensions\AutoEditVideos\System\Enums\AutoEditKindEnum;
use App\Extensions\AutoEditVideos\System\Enums\RenderStatusEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AutoEditRender extends Model
{
    protected $table = 'ext_auto_edit_videos_renders';

    protected $fillable = [
        'uuid',
        'session_id',
        'user_id',
        'team_id',
        'kind',
        'rendley_job_id',
        'title',
        'status',
        'target_resolution',
        'duration_seconds',
        'remote_url',
        'thumbnail_url',
        'local_path',
        'rendley_credits',
        'credits',
        'usage_deducted',
        'poll_attempts',
        'last_polled_at',
        'error_message',
        'completed_at',
        'payload',
    ];

    /**
     * A database default is not applied to the in-memory model after a create,
     * so `kind` and `status` would read as null until the row was refreshed —
     * and `$render->kind->entity()` would fatal on the billing path.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'kind'           => 'auto_edit',
        'status'         => 'queued',
        'usage_deducted' => false,
        'poll_attempts'  => 0,
    ];

    protected $casts = [
        'kind'             => AutoEditKindEnum::class,
        'status'           => RenderStatusEnum::class,
        'duration_seconds' => 'integer',
        'rendley_credits'  => 'float',
        'credits'          => 'float',
        'usage_deducted'   => 'boolean',
        'poll_attempts'    => 'integer',
        'last_polled_at'   => 'datetime',
        'completed_at'     => 'datetime',
        'payload'          => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(static function (self $render): void {
            if (blank($render->uuid)) {
                $render->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(AutoEditSession::class, 'session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isComplete(): bool
    {
        return $this->status === RenderStatusEnum::Completed;
    }

    public function isTerminal(): bool
    {
        return $this->status->isTerminal();
    }

    public function scopeUnfinished(Builder $query): Builder
    {
        return $query->whereIn('status', [
            RenderStatusEnum::Queued->value,
            RenderStatusEnum::Processing->value,
        ]);
    }

    /**
     * Renders that count against a plan allowance. A failed job never charged
     * the user, so it must not eat their monthly quota either.
     */
    public function scopeBillable(Builder $query): Builder
    {
        return $query->whereIn('status', [
            RenderStatusEnum::Queued->value,
            RenderStatusEnum::Processing->value,
            RenderStatusEnum::Completed->value,
        ]);
    }

    /**
     * Rendley's storage URLs expire, so prefer our own copy once it exists.
     */
    public function playbackUrl(): ?string
    {
        if (filled($this->local_path)) {
            return Storage::disk((string) config('auto-edit-videos.storage.disk', 'uploads'))->url($this->local_path);
        }

        return $this->remote_url;
    }

    public function displayTitle(): string
    {
        return $this->title ?: ($this->session?->displayTitle() ?? __('Auto edit'));
    }

    public function formattedDuration(): string
    {
        $seconds = (int) $this->duration_seconds;

        return sprintf('%02d:%02d', intdiv($seconds, 60), $seconds % 60);
    }
}
