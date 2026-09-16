<?php

namespace App\Services\Contracts;

use App\DTOs\SendMobileOtpDTO;
use App\DTOs\VerifyOtpDTO;

interface MobileVerificationServiceInterface
{
    public function sendOtp(int $userId,SendMobileOtpDTO $dto): string;

    public function verifyOtp(int $userId,VerifyOtpDTO $dto): void;
}