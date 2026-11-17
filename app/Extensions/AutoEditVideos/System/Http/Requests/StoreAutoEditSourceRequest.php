<?php

declare(strict_types=1);

namespace App\Extensions\AutoEditVideos\System\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAutoEditSourceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $maxKilobytes = (int) round(((int) config('auto-edit-videos.limits.source_max_bytes', 536870912)) / 1024);

        return [
            'file' => [
                'nullable',
                'required_without:url',
                'file',
                'mimetypes:' . implode(',', (array) config('auto-edit-videos.limits.source_mimes', [])),
                'max:' . $maxKilobytes,
            ],
            'url' => ['nullable', 'required_without:file', 'string', 'url', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'file.required_without' => __('Choose a file or paste a media URL.'),
            'file.mimetypes'        => __('That file type is not supported.'),
            'file.max'              => __('That file is too large.'),
            'url.url'               => __('That does not look like a valid media URL.'),
        ];
    }
}
