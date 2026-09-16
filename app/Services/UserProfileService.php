<?php

namespace App\Services;

use App\DTOs\UpdateProfileDTO;
use App\Repositories\Contracts\UserProfileRepositoryInterface;
use App\Services\Contracts\UserProfileServiceInterface;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UserProfileService implements UserProfileServiceInterface
{
    private const PROFILE_CACHE_TTL = 900;

    public function __construct(
        private readonly UserProfileRepositoryInterface $profileRepository,
    ) {}

    public function getProfile(int $userId): ?Profile
    {
        return Cache::store('redis')->remember($this->profileCacheKey($userId), self::PROFILE_CACHE_TTL, function () use ($userId) {
            return $this->profileRepository->findByUserId($userId);
        });
    }

    public function updateProfile(int $userId, UpdateProfileDTO $profileData): ?Profile
    {
        $profile = $this->profileRepository->findByUserId($userId);

        if (! $profile) {
            return null;
        }

        $updatedProfile = $this->profileRepository->updateProfile($profile, $profileData);
        Cache::store('redis')->forget($this->profileCacheKey($userId));

        return $updatedProfile;
    }

    public function updateStatus(int $userId, string $status): ?User
    {
        $user = $this->profileRepository->findUserById($userId);

        if (! $user) {
            return null;
        }

        $updatedUser = $this->profileRepository->updateUserStatus($user, $status);
        Cache::store('redis')->forget($this->profileCacheKey($userId));

        return $updatedUser;
    }

    public function deleteProfile(int $userId, string $reason): bool
    {
        $user = $this->profileRepository->findUserById($userId);

        if (! $user) {
            return false;
        }

        DB::transaction(fn () => $this->profileRepository->deleteUser($user, $reason));
        Cache::store('redis')->forget($this->profileCacheKey($userId));

        return true;
    }

    private function profileCacheKey(int $userId): string
    {
        return "user_profile:{$userId}";
    }

}