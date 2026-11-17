<?php

namespace App\Extensions\PhoneCallAgent\System\Models;

use App\Models\User;
use Database\Factories\ExtPhoneCallAgentCampaignFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExtPhoneCallAgentCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'agent_id',
        'user_id',
        'name',
        'status',
        'scheduled_at',
        'total_recipients',
        'completed_count',
        'failed_count',
    ];

    protected $casts = [
        'scheduled_at'     => 'datetime',
        'total_recipients' => 'integer',
        'completed_count'  => 'integer',
        'failed_count'     => 'integer',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(ExtPhoneCallAgent::class, 'agent_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function calls(): HasMany
    {
        return $this->hasMany(ExtPhoneCallAgentCall::class, 'campaign_id');
    }

    protected static function newFactory(): Factory
    {
        return ExtPhoneCallAgentCampaignFactory::new();
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
