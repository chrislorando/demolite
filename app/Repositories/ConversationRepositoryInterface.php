<?php

namespace App\Repositories;

use App\Models\Conversation;

interface ConversationRepositoryInterface
{
    public function findOrFail(string $id): Conversation;
    public function create(array $data): Conversation;
    public function update(Conversation $conversation, array $data): bool;
}
