<?php

namespace App\DTOs;

final class NotificationDTO
{
    public function __construct(
        public readonly int $userId,
        public readonly string $title,
        public readonly ?string $message = null,
        public readonly ?string $type = null,
        public readonly bool $isRead = false,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            userId: (int) ($data['user_id'] ?? 0),
            title: $data['title'] ?? '',
            message: $data['message'] ?? null,
            type: $data['type'] ?? null,
            isRead: (bool) ($data['is_read'] ?? false),
        );
    }

    public function toArray(): array
    {
        return [
            'user_id' => $this->userId,
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'is_read' => $this->isRead,
        ];
    }
}
