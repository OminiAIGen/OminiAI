<?php

declare(strict_types=1);

namespace App\Extensions\AutoEditVideos\System\Models;

use App\Extensions\AutoEditVideos\System\Enums\SourceTypeEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutoEditSource extends Model
{
    protected $table = 'ext_auto_edit_videos_sources';

    protected $fillable = [
        'session_id',
        'user_id',
        'type',
        'original_name',
        'local_path',
        'public_url',
        'mime_type',
        'size_bytes',
        'duration_seconds',
        'width',
        'height',
    ];

    protected $casts = [
        'type'             => SourceTypeEnum::class,
        'size_bytes'       => 'integer',
        'duration_seconds' => 'integer',
        'width'            => 'integer',
        'height'           => 'integer',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(AutoEditSession::class, 'session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function displayName(): string
    {
        return $this->original_name ?: (string) $this->public_url;
    }
}
