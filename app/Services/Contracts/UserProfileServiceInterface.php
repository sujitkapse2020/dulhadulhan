<?php

namespace App\Services\Contracts;

use App\DTOs\UpdateProfileDTO;
use App\Models\Profile;
use App\Models\User;

interface UserProfileServiceInterface
{
    public function getProfile(int $userId): ?Profile;
    public function updateProfile(int $userId, UpdateProfileDTO $profileData): ?Profile;
    public function updateStatus(int $userId, string $status): ?User;
    public function deleteProfile(int $userId, string $reason): bool;
}