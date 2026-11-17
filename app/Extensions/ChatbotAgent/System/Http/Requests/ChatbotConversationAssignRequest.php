<?php

namespace App\Extensions\ChatbotAgent\System\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatbotConversationAssignRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'conversation_id'   => ['required', 'integer', 'exists:ext_chatbot_conversations,id'],
            'assigned_agent_id' => ['nullable', 'integer'],
        ];
    }
}
