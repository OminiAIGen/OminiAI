<?php

declare(strict_types=1);

namespace App\Extensions\AutoEditVideos\System\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutoEditMessage extends Model
{
    public const ROLE_USER = 'user';

    public const ROLE_AGENT = 'agent';

    public const KIND_MESSAGE = 'message';

    public const KIND_PLAN = 'plan';

    public const KIND_SYSTEM = 'system';

    protected $table = 'ext_auto_edit_videos_messages';

    protected $fillable = [
        'session_id',
        'role',
        'kind',
        'body',
        'rendley_job_id',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(AutoEditSession::class, 'session_id');
    }

    public function isFromUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }
}
