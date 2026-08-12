<?php

namespace App\Services;

use App\DTOs\LoginDTO;
use App\DTOs\UserDTO;
use App\DTOs\UserProfileDTO;
use App\Events\UserRegistered;
use App\Exceptions\DuplicateAccountException;
use App\Exceptions\InvalidCredentialsException;
use App\Models\User;
use App\Models\Profile;
use App\Repositories\Contracts\LoginHistoryRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Contracts\UserProfileRepositoryInterface;
use App\Services\Contracts\AuthServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\NotificationController;
use App\Jobs\SendWelcomeEmailJob;
use Throwable;

class AuthService implements AuthServiceInterface
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserProfileRepositoryInterface $userProfileRepository,
        private readonly LoginHistoryRepositoryInterface $loginHistoryRepository
    ) {}

    public function register(UserDTO $userDTO): User
    {
        // Defense-in-depth: repo check even though the request already validated uniqueness
        if ($this->userRepository->findByEmail($userDTO->email)) {
            throw new DuplicateAccountException('An account with this email already exists.');
        }       

       $user = DB::transaction(function () use ($userDTO) {
        
                $hashedPassword = Hash::make($userDTO->password);

                return $this->userRepository->create($userDTO, $hashedPassword);
            });

            DB::afterCommit(function () use ($user) {

                SendWelcomeEmailJob::dispatch($user);

                app(NotificationController::class)->sendEmailVerificationNotification($user);
                
            });

            return $user;
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

    public function login(LoginDTO $dto, string $ip = null, string $userAgent = null): array
    {
        $user = $this->userRepository->findByEmail($dto->login)
            ?? $this->userRepository->findByPhone($dto->login);

        if (! $user || ! Hash::check($dto->password, $user->password)) {
            throw new InvalidCredentialsException('Invalid credentials.');
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        $user->update(['last_login' => now()]);

        $this->loginHistoryRepository->record($user, $ip, $userAgent);

        return [
            'user' => $user,
            'token' => $token,
            'token_type' => 'Bearer',
        ];
    }
}
