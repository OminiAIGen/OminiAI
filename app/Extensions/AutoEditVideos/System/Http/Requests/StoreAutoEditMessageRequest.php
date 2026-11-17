<?php

declare(strict_types=1);

namespace App\Extensions\AutoEditVideos\System\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAutoEditMessageRequest extends FormRequest
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
            'body' => ['required', 'string', 'min:1', 'max:' . config('auto-edit-videos.limits.prompt_max', 5000)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'body.required' => __('Tell the editor what to change.'),
        ];
    }
}
