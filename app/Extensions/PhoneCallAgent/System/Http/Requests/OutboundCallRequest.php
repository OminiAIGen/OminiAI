<?php

namespace App\Extensions\PhoneCallAgent\System\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class OutboundCallRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'agent_id'     => ['required', 'string', 'exists:ext_phone_call_agents,uuid'],
            'to_number'    => ['required', 'string', 'regex:/^\+[1-9]\d{6,14}$/'],
            'scheduled_at' => ['sometimes', 'nullable', 'date', 'after:now'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'to_number.required' => __('phone-call-agent::phone-call-agent.outbound_number_required'),
            'to_number.regex'    => __('Enter a valid phone number in E.164 format, e.g. +14155552671.'),
        ];
    }
}
