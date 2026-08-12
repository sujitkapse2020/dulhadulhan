<?php

namespace App\Repositories\Contracts;

use App\DTOs\NotificationDTO;
use App\Models\Notification;
use App\Models\User;

interface NotificationReporitoryInterface
{
	public function create(NotificationDTO $dto): Notification;
}

