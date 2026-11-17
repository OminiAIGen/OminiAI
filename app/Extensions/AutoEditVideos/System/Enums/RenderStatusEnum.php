<?php

declare(strict_types=1);

namespace App\Extensions\AutoEditVideos\System\Enums;

enum RenderStatusEnum: string
{
    case Queued = 'queued';
    case Processing = 'processing';
    case Completed = 'completed';
    case Failed = 'failed';
    case Canceled = 'canceled';

    public function label(): string
    {
        return match ($this) {
            self::Queued     => __('Queued'),
            self::Processing => __('Processing'),
            self::Completed  => __('Completed'),
            self::Failed     => __('Failed'),
            self::Canceled   => __('Canceled'),
        };
    }

    public function isTerminal(): bool
    {
        return match ($this) {
            self::Completed, self::Failed, self::Canceled => true,
            default                                       => false,
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Completed => 'success',
            self::Failed    => 'danger',
            self::Canceled  => 'secondary',
            default         => 'info',
        };
    }

    /**
     * Rendley documents exactly these five states, but normalise anything else
     * to `queued` rather than throwing on an unknown value mid-poll.
     */
    public static function fromApi(?string $status): self
    {
        return match ($status) {
            'completed', 'complete', 'succeeded', 'success' => self::Completed,
            'failed', 'error', 'prepare_failed'             => self::Failed,
            'canceled', 'cancelled'                         => self::Canceled,
            'processing', 'running', 'rendering'            => self::Processing,
            default                                         => self::Queued,
        };
    }
}
