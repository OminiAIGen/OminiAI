<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Enums;

use App\Domains\Entity\Enums\EntityEnum;

enum VideoAgentKindEnum: string
{
    case Agent = 'agent';
    case Avatar = 'avatar';
    case Image = 'image';
    case Cinematic = 'cinematic';
    case Studio = 'studio';
    case Template = 'template';
    case Lipsync = 'lipsync';
    case Translation = 'translation';
    case Clip = 'clip';
    case Filler = 'filler';
    case Proofread = 'proofread';
    case HyperFrames = 'hyperframes';
    case Tts = 'tts';

    public function label(): string
    {
        return match ($this) {
            self::Agent       => __('Video Agent'),
            self::Avatar      => __('Avatar Video'),
            self::Image       => __('Image Video'),
            self::Cinematic   => __('Cinematic Avatar'),
            self::Studio      => __('Studio Scenes'),
            self::Template    => __('Template Video'),
            self::Lipsync     => __('Lipsync'),
            self::Translation => __('Translation'),
            self::Clip        => __('AI Clip'),
            self::Filler      => __('Filler Word Removal'),
            self::Proofread   => __('Proofread'),
            self::HyperFrames => __('HyperFrames'),
            self::Tts         => __('Text to Speech'),
        };
    }

    /**
     * Which credit pool this output is billed against.
     */
    public function entity(): EntityEnum
    {
        return match ($this) {
            self::Agent   => EntityEnum::HEYGEN_VIDEO_AGENT,
            self::Lipsync => EntityEnum::HEYGEN_LIPSYNC,
            self::Tts     => EntityEnum::HEYGEN_TTS,
            default       => EntityEnum::HEYGEN_VIDEO_V3,
        };
    }

    public function isAudio(): bool
    {
        return $this === self::Tts;
    }
}
