<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoAgentMessage extends Model
{
    public const ROLE_USER = 'user';

    public const ROLE_AGENT = 'agent';

    public const KIND_MESSAGE = 'message';

    public const KIND_REVISION = 'revision';

    public const KIND_APPROVAL = 'approval';

    public const KIND_SYSTEM = 'system';

    protected $table = 'ext_video_agent_messages';

    protected $fillable = [
        'session_id',
        'role',
        'kind',
        'body',
        'heygen_message_id',
        'resource_id',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function session(): BelongsTo
    {
        return $this->belongsTo(VideoAgentSession::class, 'session_id');
    }

    public function isFromUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }
}
