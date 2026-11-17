<?php

declare(strict_types=1);

namespace App\Extensions\VideoAgent\System\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVideoAgentMessageRequest extends FormRequest
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
            'message' => ['required', 'string', 'min:1', 'max:4000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'message.required' => __('Write what you would like the agent to change.'),
        ];
    }
}
