<?php

namespace App\Repositories\Contracts;

use App\DTOs\UserProfileDTO;
use App\DTOs\UpdateProfileDTO;
use App\Models\Profile;
use App\Models\User;

interface UserProfileRepositoryInterface
{
    public function createProfile(UserProfileDTO $profileData, int $userId): Profile;
    public function findByUserId(int $userId): ?Profile;
    public function findUserById(int $userId): ?User;
    public function updateProfile(Profile $profile, UpdateProfileDTO $profileData): Profile;
    public function updateUserStatus(User $user, string $status): User;
    public function deleteUser(User $user, string $reason): void;
}