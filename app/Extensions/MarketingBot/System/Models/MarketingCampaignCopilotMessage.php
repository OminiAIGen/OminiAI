<?php

namespace App\Extensions\MarketingBot\System\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketingCampaignCopilotMessage extends Model
{
    protected $table = 'ext_marketing_campaign_copilot_messages';

    protected $fillable = [
        'campaign_id',
        'role',
        'content',
        'tool_calls',
        'metadata',
    ];

    protected $casts = [
        'tool_calls' => 'array',
        'metadata'   => 'array',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(MarketingCampaign::class, 'campaign_id', 'id');
    }
}
