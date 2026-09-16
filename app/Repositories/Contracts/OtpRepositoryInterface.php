<?php

namespace App\Repositories\Contracts;

use App\Models\Otp;
use App\Models\User;

interface OtpRepositoryInterface
{
    public function create(User $user, string $mobile, string $otp, int $expiresInMinutes): Otp;

    public function latestActiveFor(User $user, string $mobile): ?Otp;

    public function markVerified(Otp $otp): void;

    public function invalidateAllFor(User $user): void;
}