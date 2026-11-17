<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Models;

use Illuminate\Database\Eloquent\Model;

class VideoAgentWebhookEvent extends Model
{
    protected $table = 'ext_video_agent_webhook_events';

    protected $fillable = [
        'heygen_event_id',
        'event_type',
        'payload',
        'processed_at',
    ];

    protected $casts = [
        'payload'      => 'array',
        'processed_at' => 'datetime',
    ];
}
