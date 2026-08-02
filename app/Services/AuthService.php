<?php

namespace App\Services;

use App\DTOs\UserDTO;
use App\DTOs\UserProfileDTO;
use App\Events\UserRegistered;
use App\Exceptions\DuplicateAccountException;
use App\Models\User;
use App\Models\Profile;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\UserProfileRepositoryInterface;
use App\Services\Contracts\AuthServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendWelcomeEmailJob;
use Throwable;

class AuthService implements AuthServiceInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserProfileRepositoryInterface $userProfileRepository
    ) {}

    public function register(UserDTO $userDTO): User
    {
        // Defense-in-depth: repo check even though the request already validated uniqueness
        if ($this->userRepository->findByEmail($userDTO->email)) {
            throw new DuplicateAccountException('An account with this email already exists.');
        }       

        return DB::transaction(function () use ($userDTO) {
            $hashedPassword = Hash::make($userDTO->password);
            $user = $this->userRepository->create($userDTO, $hashedPassword);
           // event(new UserRegistered($user));
            SendWelcomeEmailJob::dispatch($user)->afterCommit();
            return $user;
        });
    }

    public function createProfile(UserProfileDTO $profileDTO, int $userId): Profile
    {
        try {
            return $this->userProfileRepository->createProfile($profileDTO, $userId);
        } catch (Throwable $e) {
            Log::error('Failed to create user profile', ['error' => $e->getMessage()]);
            throw new \RuntimeException('Failed to create user profile.');
        }
    }
}