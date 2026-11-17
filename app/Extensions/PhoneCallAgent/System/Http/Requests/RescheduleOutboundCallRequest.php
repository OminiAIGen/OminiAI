<?php

namespace App\Extensions\PhoneCallAgent\System\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RescheduleOutboundCallRequest extends FormRequest
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
            'scheduled_at' => ['required', 'date', 'after:now'],
        ];
    }
}
