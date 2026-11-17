<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Enums;

enum VideoStatusEnum: string
{
    case Queued = 'queued';
    case Processing = 'processing';
    case Completed = 'completed';
    case Failed = 'failed';

    public function label(): string
    {
        return match ($this) {
            self::Queued     => __('Queued'),
            self::Processing => __('Processing'),
            self::Completed  => __('Completed'),
            self::Failed     => __('Failed'),
        };
    }

    public function isTerminal(): bool
    {
        return $this === self::Completed || $this === self::Failed;
    }

    /**
     * HeyGen reports `waiting`, `pending`, `processing`, `completed`, `failed`
     * depending on the endpoint. Normalise them all here.
     */
    public static function fromApi(?string $status): self
    {
        return match ($status) {
            'completed', 'complete', 'success', 'done' => self::Completed,
            'failed', 'error'                          => self::Failed,
            'processing', 'generating', 'rendering'    => self::Processing,
            default                                    => self::Queued,
        };
    }
}
