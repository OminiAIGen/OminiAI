<?php

namespace App\Extensions\PhoneCallAgent\System\Enums;

use App\Enums\Traits\EnumTo;

enum FollowUpUnitEnum: string
{
    use EnumTo;

    case Minute = 'minute';
    case Hour = 'hour';
    case Day = 'day';

    public function toMinutes(int $value): int
    {
        return match ($this) {
            self::Minute => $value,
            self::Hour   => $value * 60,
            self::Day    => $value * 60 * 24,
        };
    }
}
