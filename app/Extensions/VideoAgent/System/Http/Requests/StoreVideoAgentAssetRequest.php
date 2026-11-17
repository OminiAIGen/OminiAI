<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVideoAgentAssetRequest extends FormRequest
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
        $maxKilobytes = (int) (((int) config('video-agent.limits.asset_max_bytes', 33554432)) / 1024);

        /** @var array<int, string> $mimeTypes */
        $mimeTypes = config('video-agent.limits.asset_mimes', []);

        return [
            'file' => [
                'required',
                'file',
                'max:' . $maxKilobytes,
                'mimetypes:' . implode(',', $mimeTypes),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'file.max'        => __('Attachments cannot be larger than 32MB.'),
            'file.mimetypes'  => __('HeyGen accepts PNG, JPEG, MP4, WebM, MP3, WAV, PDF and SRT files only.'),
        ];
    }
}
