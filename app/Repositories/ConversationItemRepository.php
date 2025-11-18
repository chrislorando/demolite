<?php

namespace App\Repositories;

use App\Models\ConversationItem;
use App\Models\Conversation;

class ConversationItemRepository implements ConversationItemRepositoryInterface
{
    public function create(Conversation $conversation, array $data): ConversationItem
    {
        return $conversation->items()->create($data);
    }

    public function whereConversationId(string $conversationId)
    {
        return ConversationItem::where('conversation_id', $conversationId);
    }

    public function updateStatusByConversationId(string $conversationId, array $statuses, string $status)
    {
        $query = ConversationItem::where('conversation_id', $conversationId)
            ->whereIn('status', $statuses);
        $query->update(['status' => $status]);
        return $query;
    }
}
