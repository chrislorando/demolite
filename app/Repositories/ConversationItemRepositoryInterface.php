<?php

namespace App\Repositories;

use App\Models\ConversationItem;
use App\Models\Conversation;

interface ConversationItemRepositoryInterface
{
    public function create(Conversation $conversation, array $data): ConversationItem;
    public function whereConversationId(string $conversationId);
    public function updateStatusByConversationId(string $conversationId, array $statuses, string $status);
}
