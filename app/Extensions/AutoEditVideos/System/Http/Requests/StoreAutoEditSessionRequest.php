<?php

declare(strict_types=1);

namespace App\Extensions\AutoEditVideos\System\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAutoEditSessionRequest extends FormRequest
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
        return [
            'prompt'            => ['required', 'string', 'min:' . config('auto-edit-videos.limits.prompt_min', 1), 'max:' . config('auto-edit-videos.limits.prompt_max', 5000)],
            'instructions'      => ['nullable', 'string', 'max:2000'],
            'duration'          => ['nullable', 'integer', Rule::in(config('auto-edit-videos.durations', []))],
            'target_resolution' => ['nullable', 'string', Rule::in(config('auto-edit-videos.resolutions', []))],
            'aspect_ratio'      => ['nullable', 'string', Rule::in(config('auto-edit-videos.aspect_ratios', []))],
            'source_ids'        => ['required', 'array', 'min:1', 'max:' . config('auto-edit-videos.limits.max_sources', 10)],
            'source_ids.*'      => ['integer', 'exists:ext_auto_edit_videos_sources,id'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'prompt.required'     => __('Describe the edit you want before starting.'),
            'source_ids.required' => __('Add at least one video, audio or image file before starting an edit.'),
            'source_ids.max'      => __('You can use up to :max files in a single edit.', ['max' => config('auto-edit-videos.limits.max_sources', 10)]),
        ];
    }
}
