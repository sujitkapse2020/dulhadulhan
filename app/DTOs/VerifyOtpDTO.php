<?php

namespace App\DTOs;

final class VerifyOtpDTO
{
    public function __construct(
        public readonly int $userId,
        public readonly string $otp,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'],
            otp: $data['otp'],
        );
    }
}