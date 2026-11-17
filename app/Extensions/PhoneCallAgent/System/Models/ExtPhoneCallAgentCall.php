<?php

namespace App\Extensions\PhoneCallAgent\System\Models;

use App\Extensions\PhoneCallAgent\System\Enums\CallSourceEnum;
use App\Extensions\PhoneCallAgent\System\Enums\CallStatusEnum;
use App\Extensions\PhoneCallAgent\System\Enums\DirectionEnum;
use App\Models\User;
use Database\Factories\ExtPhoneCallAgentCallFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExtPhoneCallAgentCall extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'agent_id',
        'campaign_id',
        'booking_id',
        'user_id',
        'call_sid',
        'caller_number',
        'called_number',
        'direction',
        'source',
        'status',
        'status_reason',
        'follow_up_instruction',
        'duration',
        'started_at',
        'scheduled_at',
        'ended_at',
        'pinned',
    ];

    protected $casts = [
        'status'       => CallStatusEnum::class,
        'direction'    => DirectionEnum::class,
        'source'       => CallSourceEnum::class,
        'started_at'   => 'datetime',
        'scheduled_at' => 'datetime',
        'ended_at'     => 'datetime',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(ExtPhoneCallAgent::class, 'agent_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(ExtPhoneCallAgentCampaign::class, 'campaign_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(ExtPhoneCallAgentBooking::class, 'booking_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transcripts(): HasMany
    {
        return $this->hasMany(ExtPhoneCallAgentTranscript::class, 'call_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            ExtPhoneCallAgentCallTag::class,
            'ext_phone_call_agent_call_tag_pivot',
            'call_id',
            'tag_id'
        );
    }

    protected static function newFactory(): Factory
    {
        return ExtPhoneCallAgentCallFactory::new();
    }
}
