<?php

namespace App\Repositories\Contracts;

use App\DTOs\UserDTO;
use App\Models\User;

interface UserRepositoryInterface
{
    public function create(UserDTO $dto, string $hashedPassword): User;

    public function findByEmail(string $email): ?User;

    public function findByPhone(string $phone): ?User;

    public static function generateProfileNo(): string;
}