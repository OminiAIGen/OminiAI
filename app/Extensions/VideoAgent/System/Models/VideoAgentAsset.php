<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoAgentAsset extends Model
{
    protected $table = 'ext_video_agent_assets';

    protected $fillable = [
        'user_id',
        'heygen_asset_id',
        'name',
        'url',
        'mime_type',
        'size_bytes',
        'local_path',
    ];

    protected $casts = [
        'size_bytes' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isImage(): bool
    {
        return str_starts_with((string) $this->mime_type, 'image/');
    }
}
