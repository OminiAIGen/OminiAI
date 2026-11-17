<?php

namespace App\Extensions\PhoneCallAgent\System\Enums;

use App\Enums\Traits\EnumTo;

enum DirectionEnum: string
{
    use EnumTo;

    case Inbound = 'inbound';
    case Outbound = 'outbound';
}
