<?php

namespace App\Repositories;

use App\DTOs\NotificationDTO;
use App\Models\Notification;
use App\Models\User;
use App\Repositories\Contracts\NotificationReporitoryInterface;

class NotificationRepository implements NotificationReporitoryInterface
{
    public function create(NotificationDTO $dto): Notification
    {
        $data = $dto->toArray();

        return Notification::create([
            'user_id' => $data['user_id'],
            'title' => $data['title'] ?? null,
            'message' => $data['message'] ?? null,
            'type' => $data['type'] ?? null,
            'is_read' => $data['is_read'] ?? false,
        ]);
    }
}
