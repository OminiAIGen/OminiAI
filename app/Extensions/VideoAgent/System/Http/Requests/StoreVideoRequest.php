<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Http\Requests;

use App\Extensions\VideoAgent\System\Models\VideoAgentAvatar;
use App\Extensions\VideoAgent\System\Services\VideoCreationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        $type = (string) $this->input('type', VideoCreationService::TYPE_AVATAR);

        return array_merge([
            'type' => ['required', 'string', Rule::in([
                VideoCreationService::TYPE_AVATAR,
                VideoCreationService::TYPE_IMAGE,
                VideoCreationService::TYPE_CINEMATIC,
                VideoCreationService::TYPE_STUDIO,
            ])],
            'title'        => ['nullable', 'string', 'max:191'],
            'resolution'   => ['nullable', 'string', Rule::in(['720p', '1080p', '4k'])],
            'aspect_ratio' => ['nullable', 'string', Rule::in(['16:9', '9:16', '4:5', '5:4', '1:1', 'auto'])],
        ], $this->rulesForType($type));
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    private function rulesForType(string $type): array
    {
        return match ($type) {
            VideoCreationService::TYPE_IMAGE => [
                'image_url'      => ['required_without:image_asset_id', 'nullable', 'url', 'max:2048'],
                'image_asset_id' => ['required_without:image_url', 'nullable', 'string', 'max:191'],
                'script'         => ['required', 'string', 'max:10000'],
                'voice_id'       => ['required', 'string', 'max:191'],
                'motion_prompt'  => ['nullable', 'string', 'max:1000'],
            ],

            VideoCreationService::TYPE_CINEMATIC => [
                'prompt'         => ['required', 'string', 'max:5000'],
                'avatar_ids'     => ['required', 'array', 'min:1', 'max:3'],
                'avatar_ids.*'   => ['string', 'max:191', VideoAgentAvatar::selectionRule((int) $this->user()?->id)],
                'duration'       => ['nullable', 'integer', 'min:4', 'max:15'],
                'enhance_prompt' => ['nullable', 'boolean'],
            ],

            VideoCreationService::TYPE_STUDIO => [
                'scenes'              => ['required', 'array', 'min:1', 'max:50'],
                'scenes.*.type'       => ['nullable', 'string', Rule::in(['avatar_video', 'image', 'video'])],
                'scenes.*.avatar_id'  => ['nullable', 'string', 'max:191', VideoAgentAvatar::selectionRule((int) $this->user()?->id)],
                'scenes.*.voice_id'   => ['nullable', 'string', 'max:191'],
                'scenes.*.script'     => ['nullable', 'string', 'max:10000'],
                'scenes.*.image_url'  => ['nullable', 'url', 'max:2048'],
            ],

            default => [
                'avatar_id'         => ['required', 'string', 'max:191', VideoAgentAvatar::selectionRule((int) $this->user()?->id)],
                'script'            => ['required', 'string', 'max:10000'],
                'voice_id'          => ['required', 'string', 'max:191'],
                'engine'            => ['nullable', 'string', Rule::in(['avatar_iii', 'avatar_iv', 'avatar_v'])],
                'motion_prompt'     => ['nullable', 'string', 'max:1000'],
                'remove_background' => ['nullable', 'boolean'],
            ],
        };
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'avatar_ids.max'  => __('A cinematic avatar accepts at most 3 looks.'),
            'duration.max'    => __('Cinematic avatar videos are between 4 and 15 seconds long.'),
            'scenes.max'      => __('A studio video can have at most 50 scenes.'),
        ];
    }
}
