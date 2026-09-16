<?php

namespace App\Repositories;

use App\DTOs\UserDTO;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Str;

class UserRepository implements UserRepositoryInterface
{
    public function create(UserDTO $dto, string $hashedPassword): User
    {   
        return User::create([
            'uuid' => Str::uuid(),
            'profile_no' => self::generateProfileNo(),
            'name' => $dto->name,
            'email' => $dto->email,
            'mobile' => $dto->mobile,
            'password' => $hashedPassword,
        ]);
    }

    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function findByPhone(string $mobile): ?User
    {
        return User::where('mobile', $mobile)->first();
    }

    public static function generateProfileNo(): string
    {
        do {
            $profileNo = 'DD' . strtoupper(Str::random(6));
        } while (
            User::where('profile_no', $profileNo)->exists()
        );

        return $profileNo;
    }
}