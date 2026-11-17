<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Enums;

enum SessionModeEnum: string
{
    case Generate = 'generate';
    case Chat = 'chat';

    public function label(): string
    {
        return match ($this) {
            self::Generate => __('One-shot'),
            self::Chat     => __('Review storyboard'),
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Generate => __('Render immediately without a review checkpoint.'),
            self::Chat     => __('Show a scene-by-scene storyboard for approval before rendering.'),
        };
    }
}
