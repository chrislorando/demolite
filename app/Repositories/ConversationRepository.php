<?php

namespace App\Repositories;

use App\Models\Conversation;

class ConversationRepository implements ConversationRepositoryInterface
{
    public function findOrFail(string $id): Conversation
    {
        return Conversation::findOrFail($id);
    }

    public function create(array $data): Conversation
    {
        return Conversation::create($data);
    }

    public function update(Conversation $conversation, array $data): bool
    {
        return $conversation->update($data);
    }
}
