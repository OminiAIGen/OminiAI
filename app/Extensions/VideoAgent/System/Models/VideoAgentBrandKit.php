<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoAgentBrandKit extends Model
{
    public const TYPE_KIT = 'kit';

    public const TYPE_GLOSSARY = 'glossary';

    protected $table = 'ext_video_agent_brand_kits';

    protected $fillable = [
        'user_id',
        'heygen_id',
        'type',
        'name',
        'payload',
        'synced_at',
    ];

    protected $casts = [
        'payload'   => 'array',
        'synced_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeKits(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_KIT);
    }

    public function scopeGlossaries(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_GLOSSARY);
    }
}
