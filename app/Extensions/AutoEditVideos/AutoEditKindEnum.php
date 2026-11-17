<?php

declare(strict_types=1);

namespace App\Extensions\AutoEditVideos\System\Enums;

use App\Domains\Entity\Enums\EntityEnum;

enum AutoEditKindEnum: string
{
    case AutoEdit = 'auto_edit';

    public function label(): string
    {
        return match ($this) {
            self::AutoEdit => __('Auto Edit'),
        };
    }

    /**
     * Which credit pool this output is billed against.
     */
    public function entity(): EntityEnum
    {
        return match ($this) {
            self::AutoEdit => EntityEnum::RENDLEY_AUTO_EDIT,
        };
    }
}
