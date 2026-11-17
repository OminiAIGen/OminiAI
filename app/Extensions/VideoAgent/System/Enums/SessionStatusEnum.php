<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Enums;

enum SessionStatusEnum: string
{
    case Thinking = 'thinking';
    case WaitingForInput = 'waiting_for_input';
    case Reviewing = 'reviewing';
    case Generating = 'generating';
    case Completed = 'completed';
    case Failed = 'failed';
    case Stopped = 'stopped';

    public function label(): string
    {
        return match ($this) {
            self::Thinking        => __('Thinking'),
            self::WaitingForInput => __('Waiting for your input'),
            self::Reviewing       => __('Storyboard ready for review'),
            self::Generating      => __('Rendering'),
            self::Completed       => __('Completed'),
            self::Failed          => __('Failed'),
            self::Stopped         => __('Stopped'),
        };
    }

    public function isTerminal(): bool
    {
        return match ($this) {
            self::Completed, self::Failed, self::Stopped => true,
            default                                      => false,
        };
    }

    /**
     * The agent is idle and cannot progress without the user acting on it.
     */
    public function needsUser(): bool
    {
        return match ($this) {
            self::WaitingForInput, self::Reviewing => true,
            default                                => false,
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Completed                        => 'success',
            self::Failed                           => 'danger',
            self::Stopped                          => 'secondary',
            self::Reviewing, self::WaitingForInput => 'warning',
            default                                => 'info',
        };
    }

    public static function fromApi(?string $status): self
    {
        return self::tryFrom((string) $status) ?? self::Thinking;
    }
}
