<?php

namespace App\Repositories;

use App\Models\Otp;
use App\Models\User;
use App\Repositories\Contracts\OtpRepositoryInterface;
use Illuminate\Support\Carbon;

class OtpRepository implements OtpRepositoryInterface
{
    public function create(User $user, string $mobile, string $otp, int $expiresInMinutes): Otp
    {
        return Otp::create([
            'user_id' => $user->id,
            'mobile' => $mobile,
            'otp' => $otp,
            'expires_at' => now()->addMinutes($expiresInMinutes),
        ]);
    }

    public function latestActiveFor(User $user, string $mobile): ?Otp
    {
        return Otp::where('user_id', $user->id)
            ->where('mobile', $mobile)
            ->where('verified', false)
            ->where('expires_at', '>', Carbon::now())
            ->latest()
            ->first();
    }

    public function markVerified(Otp $otp): void
    {
        $otp->update(['verified' => true]);
    }

    public function invalidateAllFor(User $user): void
    {
        Otp::where('user_id', $user->id)
            ->where('verified', false)
            ->update(['expires_at' => now()]);
    }
}