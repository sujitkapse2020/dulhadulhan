<?php

namespace App\Repositories\Contracts;

use App\DTOs\UserProfileDTO;
use App\Models\Profile;

interface UserProfileRepositoryInterface
{
    public function createProfile(UserProfileDTO $profileData, int $userId): Profile;
}