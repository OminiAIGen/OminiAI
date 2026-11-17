<?php

namespace App\Extensions\PhoneCallAgent\System\Models;

use App\Models\User;
use Database\Factories\ExtPhoneCallAgentBookingFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExtPhoneCallAgentBooking extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'agent_id',
        'user_id',
        'provider',
        'provider_uid',
        'customer_name',
        'customer_email',
        'customer_phone',
        'starts_at',
        'ends_at',
        'timezone',
        'status',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
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
        return $this->hasMany(ExtPhoneCallAgentCall::class, 'booking_id');
    }

    protected static function newFactory(): Factory
    {
        return ExtPhoneCallAgentBookingFactory::new();
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
