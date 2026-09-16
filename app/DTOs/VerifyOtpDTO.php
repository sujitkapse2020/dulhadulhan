<?php

namespace App\DTOs;

final class VerifyOtpDTO
{
    public function __construct(
        public readonly string $mobile,
        public readonly string $otp,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            mobile: $data['mobile'],
            otp: $data['otp'],
        );
    }
}