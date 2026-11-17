<?php

namespace App\Extensions\PhoneCallAgent\System\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOutboundCampaignRequest extends FormRequest
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
            'name'         => ['required', 'string', 'max:255'],
            'numbers'      => ['required', 'array', 'min:1', 'max:1000'],
            'numbers.*'    => ['required', 'string', 'regex:/^\+[1-9]\d{6,14}$/'],
            'scheduled_at' => ['sometimes', 'nullable', 'date', 'after:now'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'numbers.required' => __('phone-call-agent::phone-call-agent.campaign_no_numbers'),
            'numbers.*.regex'  => __('Enter valid phone numbers in E.164 format, e.g. +14155552671.'),
        ];
    }
}
