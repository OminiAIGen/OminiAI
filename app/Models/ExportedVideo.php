<?php

namespace App\Models;

use App\Enums\AiInfluencer\VideoStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExportedVideo extends Model
{
    use HasFactory;

    protected $fillable = ['video_url', 'used_ai_tool', 'cover_url', 'video_duration', 'title', 'task_id', 'status', 'credits_used'];

    protected $casts = [
        'status'       => VideoStatusEnum::class,
        'credits_used' => 'float',
    ];
}
