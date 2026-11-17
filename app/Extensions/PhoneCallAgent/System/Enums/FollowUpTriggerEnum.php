<?php

namespace App\Extensions\PhoneCallAgent\System\Enums;

use App\Enums\Traits\EnumTo;

enum FollowUpTriggerEnum: string
{
    use EnumTo;

    case BeforeBooking = 'before_booking';
    case AfterBooking = 'after_booking';
    case AfterCall = 'after_call';
    case OnCancel = 'on_cancel';
}
