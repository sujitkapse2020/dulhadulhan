<?php

namespace App\Services\Contracts;

use App\DTOs\UserDTO;
use App\DTOs\UserProfileDTO;
use App\Models\Profile;
use App\Models\User;

interface AuthServiceInterface
{
    public function register(UserDTO $dto): User;
    public function createProfile(UserProfileDTO $profileDTO, int $userId): Profile;
}