<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDigitalTwinRequest extends FormRequest
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
        return [
            'name'     => ['required', 'string', 'max:191'],
            'asset_id' => [
                'required', 'string', 'max:191',
                Rule::exists('ext_video_agent_assets', 'heygen_asset_id')
                    ->where('user_id', $this->user()?->id)
                    ->whereIn('mime_type', ['video/mp4', 'video/webm']),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'asset_id.exists' => __('Upload an MP4 or WebM video that belongs to your account.'),
        ];
    }
}
