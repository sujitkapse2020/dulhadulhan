<?php

namespace App\Repositories\Contracts;

use App\Models\PhoneOtp;
use App\Models\User;

interface OtpRepositoryInterface
{
    public function create(User $user, string $otpHash, int $expiresInMinutes): PhoneOtp;

    public function latestActiveFor(User $user): ?PhoneOtp;

    public function incrementAttempts(PhoneOtp $otp): void;

    public function markVerified(PhoneOtp $otp): void;

    public function invalidateAllFor(User $user): void;
}