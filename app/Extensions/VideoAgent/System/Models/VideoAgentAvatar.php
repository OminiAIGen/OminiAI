<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Models;

use App\Models\User;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoAgentAvatar extends Model
{
    protected $table = 'ext_video_agent_avatars';

    protected $fillable = [
        'user_id',
        'heygen_id',
        'group_id',
        'name',
        'avatar_type',
        'ownership',
        'gender',
        'status',
        'consent_status',
        'preview_image_url',
        'preview_video_url',
        'default_voice_id',
        'preferred_orientation',
        'supported_api_engines',
        'tags',
        'payload',
        'synced_at',
        'last_polled_at',
    ];

    protected $casts = [
        'supported_api_engines' => 'array',
        'tags'                  => 'array',
        'payload'               => 'array',
        'synced_at'             => 'datetime',
        'last_polled_at'        => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeVisibleTo(Builder $query, int $userId): Builder
    {
        return $query->where(function (Builder $query) use ($userId): void {
            $query->where(fn (Builder $query) => $query->where('ownership', 'public')->whereNull('user_id'))
                ->orWhere('user_id', $userId);
        });
    }

    public static function selectionRule(int $userId): Closure
    {
        return static function (string $attribute, mixed $value, Closure $fail) use ($userId): void {
            $avatar = is_string($value)
                ? self::query()->visibleTo($userId)->where('heygen_id', $value)->first()
                : null;

            if ($avatar === null || ! $avatar->isReady()) {
                $fail(__('Select an available avatar.'));
            }
        };
    }

    public function scopeOwnedBy(Builder $query, ?string $ownership): Builder
    {
        return blank($ownership) ? $query : $query->where('ownership', $ownership);
    }

    public function scopeOfType(Builder $query, ?string $type): Builder
    {
        return blank($type) ? $query : $query->where('avatar_type', $type);
    }

    public function scopeReady(Builder $query): Builder
    {
        return $query
            ->where(fn (Builder $query) => $query->whereNull('status')->orWhere('status', '')->orWhere('status', 'completed'))
            ->where(function (Builder $query): void {
                $query->whereNull('avatar_type')
                    ->orWhere('avatar_type', '!=', 'digital_twin')
                    ->orWhere('consent_status', 'accepted')
                    ->orWhere(fn (Builder $query) => $query->where('ownership', 'public')->whereNull('user_id'));
            });
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return blank($term) ? $query : $query->where('name', 'like', '%' . $term . '%');
    }

    public function isReady(): bool
    {
        $trained = blank($this->status) || $this->status === 'completed';

        return $trained && (
            $this->avatar_type !== 'digital_twin'
            || ($this->ownership === 'public' && $this->user_id === null)
            || $this->consent_status === 'accepted'
        );
    }
}
