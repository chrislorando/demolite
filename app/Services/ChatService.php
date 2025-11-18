<?php

namespace App\Services;

use App\Enums\ResponseStatus;
use App\Repositories\ConversationRepository;
use App\Repositories\ConversationItemRepository;

class ChatService
{
    public function __construct(
        protected \App\Repositories\ConversationRepositoryInterface $conversationRepository,
        protected \App\Repositories\ConversationItemRepositoryInterface $conversationItemRepository
    ) {}

    public function getConversationById(string $conversationId): \App\Models\Conversation
    {
        return $this->conversationRepository->findOrFail($conversationId);
    }

    public function getPersonalizationForConversation(string $conversationId)
    {
        $conversation = $this->getConversationById($conversationId);
        return $conversation->user->personalization()->where('status', 'active')->first();
    }

    public function createUserMessage(string $conversationId, string $content): \App\Models\ConversationItem
    {
        $conversation = $this->conversationRepository->findOrFail($conversationId);

        $userMessage = $this->conversationItemRepository->create($conversation, [
            'role' => 'user',
            'content' => $content,
        ]);

        if (! $conversation->title) {
            $this->conversationRepository->update($conversation, [
                'title' => $this->generateTitle($content),
            ]);
        }

        return $userMessage;
    }

    public function createConversation(?int $userId = null): \App\Models\Conversation
    {
        return $this->conversationRepository->create([
            'user_id' => $userId,
            'title' => null,
        ]);
    }

    public function createAssistantMessage(string $conversationId, string $content, ?string $modelId = null, ?string $responseId = null): \App\Models\ConversationItem
    {
        $conversation = $this->conversationRepository->findOrFail($conversationId);

        return $this->conversationItemRepository->create($conversation, [
            'role' => 'assistant',
            'content' => $content,
            'model_id' => $modelId,
            'response_id' => $responseId,
            'status' => ResponseStatus::Created,
        ]);
    }

    public function getConversationMessages(string $conversationId): array
    {
        $conversation = $this->conversationRepository->findOrFail($conversationId);

        return $conversation->items()
            ->orderBy('created_at')
            ->get()
            ->map(fn ($message) => [
                'role' => $message->role,
                'content' => $message->content,
            ])
            ->toArray();
    }

    public function changeStatus(string $conversationId, string $status)
    {
        return $this->conversationItemRepository->updateStatusByConversationId(
            $conversationId,
            [ResponseStatus::Created, ResponseStatus::InProgress],
            $status
        );
    }

    private function generateTitle(string $content): string
    {
        return substr(ucfirst($content), 0, 50).(strlen($content) > 50 ? '...' : '');
    }
}
