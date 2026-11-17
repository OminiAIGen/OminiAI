<?php

declare(strict_types=1);

namespace App\Extensions\AutoEditVideos\System\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAutoEditVideosSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && method_exists($user, 'isAdmin') && $user->isAdmin();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'auto_edit_videos_api_key'         => ['nullable', 'string', 'max:255'],
            'auto_edit_videos_agent_url'       => ['nullable', 'string', 'url', 'max:255'],
            'auto_edit_videos_public_base_url' => ['nullable', 'string', 'url', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'auto_edit_videos_agent_url.url'       => __('The agent endpoint must be a full URL.'),
            'auto_edit_videos_public_base_url.url' => __('The public media URL must be a full URL.'),
        ];
    }
}
