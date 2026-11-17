<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Models;

use App\Extensions\VideoAgent\System\Enums\SessionStatusEnum;
use App\Extensions\VideoAgent\System\Enums\VideoAgentKindEnum;
use App\Extensions\VideoAgent\System\Enums\VideoStatusEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @property VideoStatusEnum $status
 * @property VideoAgentKindEnum $kind
 */
class VideoAgentVideo extends Model
{
    public const STORAGE_LOCAL = 'public';

    public const STORAGE_AWS = 's3';

    public const STORAGE_R2 = 'r2';

    protected $table = 'ext_video_agent_videos';

    protected $fillable = [
        'uuid',
        'user_id',
        'team_id',
        'session_id',
        'kind',
        'heygen_id',
        'title',
        'status',
        'duration_seconds',
        'remote_url',
        'thumbnail_url',
        'local_path',
        'storage',
        'heygen_page_url',
        'heygen_share_url',
        'credits',
        'usage_deducted',
        'poll_attempts',
        'last_polled_at',
        'payload',
        'error_message',
        'completed_at',
    ];

    protected $casts = [
        'kind'             => VideoAgentKindEnum::class,
        'status'           => VideoStatusEnum::class,
        'duration_seconds' => 'integer',
        'credits'          => 'float',
        'usage_deducted'   => 'boolean',
        'poll_attempts'    => 'integer',
        'last_polled_at'   => 'datetime',
        'payload'          => 'array',
        'completed_at'     => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $video): void {
            $video->uuid ??= (string) Str::uuid();
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

    public function session(): BelongsTo
    {
        return $this->belongsTo(VideoAgentSession::class, 'session_id');
    }

    public function scopeUnfinished(Builder $query): Builder
    {
        return $query->whereIn('status', [
            VideoStatusEnum::Queued->value,
            VideoStatusEnum::Processing->value,
        ]);
    }

    public function scopeVisibleToUser(Builder $query): Builder
    {
        return $query->where(function (Builder $query): void {
            $query->where('status', '!=', VideoStatusEnum::Queued->value)
                ->orWhereDoesntHave('session', fn (Builder $session): Builder => $session->whereIn('status', [
                    SessionStatusEnum::WaitingForInput->value,
                    SessionStatusEnum::Reviewing->value,
                ]));
        });
    }

    public function isReservedForApproval(): bool
    {
        return $this->status === VideoStatusEnum::Queued
            && $this->session?->status->needsUser() === true;
    }

    public function scopeBillable(Builder $query): Builder
    {
        return $query->where('status', '!=', VideoStatusEnum::Failed->value);
    }

    /**
     * Prefer the locally stored copy: HeyGen download URLs are presigned and
     * expire quickly.
     *
     * Local files live under public/uploads, which is the `uploads` disk in
     * this app — not the stock Laravel `public` disk (its URL points at
     * /public, which nothing serves).
     */
    public function playbackUrl(): ?string
    {
        if (blank($this->local_path)) {
            return $this->remote_url;
        }

        return match ($this->storage) {
            self::STORAGE_AWS, self::STORAGE_R2 => $this->local_path,
            default                             => Storage::disk('uploads')->url($this->local_path),
        };
    }

    public function formattedDuration(): string
    {
        $seconds = $this->duration_seconds ?? 0;

        return sprintf('%02d:%02d', intdiv($seconds, 60), $seconds % 60);
    }

    public function displayTitle(): string
    {
        return $this->title ?: $this->kind->label();
    }

    /**
     * HeyGen thumbnail URLs are presigned and stop working after `Expires`.
     * New completions cache the still locally, but older rows still point at
     * dead links — hand those back as null so cards fall back to the video
     * preview instead of a broken image.
     */
    public function displayThumbnailUrl(): ?string
    {
        $url = $this->thumbnail_url;

        if (blank($url)) {
            return null;
        }

        $expires = null;
        parse_str((string) parse_url($url, PHP_URL_QUERY), $query);

        if (isset($query['Expires']) && is_numeric($query['Expires'])) {
            $expires = (int) $query['Expires'];
        }

        if ($expires !== null && $expires <= now()->getTimestamp()) {
            return null;
        }

        return $url;
    }
}
