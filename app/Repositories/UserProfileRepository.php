<?php

namespace App\Repositories;

use App\DTOs\UserProfileDTO;
use App\Models\Profile;
use App\Repositories\Contracts\UserProfileRepositoryInterface;
use Illuminate\Support\Str;

class UserProfileRepository implements UserProfileRepositoryInterface
{
    public function createProfile(UserProfileDTO $profileDTO, int $userId): Profile
    {
        return Profile::create([
            'user_id' => $userId,
            'first_name' => $profileDTO->firstName,
            'last_name' => $profileDTO->lastName,
            'gender' => $profileDTO->gender,
            'dob' => $profileDTO->dateOfBirth->toDateString(),
            'profile_for' => $profileDTO->profileFor,
        ]);
    }
}