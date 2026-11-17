<?php

declare(strict_types=1);

namespace App\Extensions\AutoEditVideos\System\Enums;

enum SessionStatusEnum: string
{
    case Planning = 'planning';
    case Ready = 'ready';
    case Rendering = 'rendering';
    case Completed = 'completed';
    case Failed = 'failed';
    case Canceled = 'canceled';

    public function label(): string
    {
        return match ($this) {
            self::Planning  => __('Planning the edit'),
            self::Ready     => __('Ready to export'),
            self::Rendering => __('Rendering'),
            self::Completed => __('Completed'),
            self::Failed    => __('Failed'),
            self::Canceled  => __('Canceled'),
        };
    }

    public function isTerminal(): bool
    {
        return match ($this) {
            self::Failed, self::Canceled => true,
            default                      => false,
        };
    }

    /**
     * The agent is idle and the user has to act before anything else happens.
     */
    public function needsUser(): bool
    {
        return $this === self::Ready || $this === self::Completed;
    }

    /**
     * Whether the session is still waiting on a Rendley agent job.
     */
    public function isAwaitingAgent(): bool
    {
        return $this === self::Planning;
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Completed                 => 'success',
            self::Failed                    => 'danger',
            self::Canceled                  => 'secondary',
            self::Ready                     => 'warning',
            self::Planning, self::Rendering => 'info',
        };
    }
}
