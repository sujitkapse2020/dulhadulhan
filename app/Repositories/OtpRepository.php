<?php

namespace App\Repositories;

use App\Models\PhoneOtp;
use App\Models\User;
use App\Repositories\Contracts\OtpRepositoryInterface;
use Illuminate\Support\Carbon;

class OtpRepository implements OtpRepositoryInterface
{
    public function create(User $user, string $otpHash, int $expiresInMinutes): PhoneOtp
    {
        return PhoneOtp::create([
            'user_id' => $user->id,
            'otp_hash' => $otpHash,
            'expires_at' => now()->addMinutes($expiresInMinutes),
        ]);
    }

    public function latestActiveFor(User $user): ?PhoneOtp
    {
        return PhoneOtp::where('user_id', $user->id)
            ->whereNull('verified_at')
            ->where('expires_at', '>', Carbon::now())
            ->latest()
            ->first();
    }

    public function incrementAttempts(PhoneOtp $otp): void
    {
        $otp->increment('attempts');
    }

    public function markVerified(PhoneOtp $otp): void
    {
        $otp->update(['verified_at' => now()]);
    }

    public function invalidateAllFor(User $user): void
    {
        PhoneOtp::where('user_id', $user->id)
            ->whereNull('verified_at')
            ->update(['expires_at' => now()]);
    }
}