<?php

namespace App\Repositories;

use App\DTOs\UserProfileDTO;
use App\DTOs\UpdateProfileDTO;
use App\Models\Profile;
use App\Models\User;
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

    public function findByUserId(int $userId): ?Profile
    {
        return Profile::where('user_id', $userId)->first();
    }

    public function findUserById(int $userId): ?User
    {
        return User::find($userId);
    }

    public function updateProfile(Profile $profile, UpdateProfileDTO $profileData): Profile
    {
        $profile->update($profileData->attributes);

        return $profile->refresh();
    }

    public function updateUserStatus(User $user, string $status): User
    {
        $user->update(['status' => $status]);

        return $user->refresh();
    }

    public function deleteUser(User $user, string $reason): void
    {
        $this->findByUserId($user->id)?->delete();
        $user->update(['delete_reason' => $reason]);
        $user->tokens()->delete();
        $user->delete();
    }
}