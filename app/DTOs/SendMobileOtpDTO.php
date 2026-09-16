<?php

namespace App\DTOs;

final class SendMobileOtpDTO
{
    public function __construct(
        public readonly string $mobile,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self($data['mobile']);
    }
}