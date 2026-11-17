<?php

namespace App\Extensions\Chatbot\System\Policies;

use App\Extensions\Chatbot\System\Models\ChatbotConversation;
use App\Models\User;

class ChatbotConversationPolicy
{
    public function access(User $user, ChatbotConversation $conversation): bool
    {
        return in_array($conversation->chatbot_id, $user->accessibleChatbotIds(), true);
    }
}
