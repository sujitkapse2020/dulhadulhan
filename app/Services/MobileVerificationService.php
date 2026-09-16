<?php

namespace App\Services;

use App\DTOs\SendMobileOtpDTO;
use App\DTOs\VerifyOtpDTO;
use App\Exceptions\InvalidCredentialsException;
use App\Models\User;
use App\Repositories\Contracts\OtpRepositoryInterface;
use App\Services\Contracts\MobileVerificationServiceInterface;
use App\Services\Contracts\SmsServiceInterface;
use Illuminate\Support\Facades\DB;

class MobileVerificationService implements MobileVerificationServiceInterface
{
    private const OTP_EXPIRY_MINUTES = 2;

    public function __construct(
        private readonly OtpRepositoryInterface $otpRepository,
        private readonly SmsServiceInterface $smsService,
    ) {}

    public function sendOtp(int $userId,SendMobileOtpDTO $dto): string
    {
        $user = User::findOrFail($userId);
        abort_unless($user->mobile === $dto->mobile, 422, 'Mobile number does not belong to this user.');

        $otp = (string) random_int(100000, 999999);

        DB::transaction(function () use ($user, $dto, $otp): void {
            $this->otpRepository->invalidateAllFor($user);
            $this->otpRepository->create($user, $dto->mobile, $otp, self::OTP_EXPIRY_MINUTES);
        });

        $this->smsService->send($dto->mobile, "Your verification code is {$otp}. It expires in 2 minutes.");

        return $otp;
    }

    public function verifyOtp(int $userId,VerifyOtpDTO $dto): void
    {
        $user = User::findOrFail($userId);
        $otp = $this->otpRepository->latestActiveFor($user, $dto->mobile);

        if (! $otp || ! hash_equals($otp->otp, $dto->otp)) {
            throw new InvalidCredentialsException('Invalid or expired OTP.');
        }

        DB::transaction(function () use ($otp, $user): void {
            $this->otpRepository->markVerified($otp);
            $user->update(['mobile_verified_at' => now()]);
        });
    }
}