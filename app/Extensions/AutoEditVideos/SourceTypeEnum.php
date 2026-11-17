<?php

declare(strict_types=1);

namespace App\Extensions\AutoEditVideos\System\Enums;

enum SourceTypeEnum: string
{
    case Upload = 'upload';
    case Url = 'url';

    public function label(): string
    {
        return match ($this) {
            self::Upload => __('Uploaded file'),
            self::Url    => __('Linked URL'),
        };
    }
}
