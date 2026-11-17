<?php

namespace App\Extensions\PhoneCallAgent\System\Enums;

use App\Enums\Traits\EnumTo;

enum CallSourceEnum: string
{
    use EnumTo;

    case Manual = 'manual';
    case Campaign = 'campaign';
    case FollowUp = 'follow_up';
}
