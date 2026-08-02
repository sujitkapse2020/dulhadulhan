<?php

namespace App\DTOs;

use Carbon\Carbon;

final class UserDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $mobile,
        public readonly string $password
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['first_name'].' '.$data['last_name'],
            email: strtolower(trim($data['email'])),
            mobile: $data['mobile'],
            password: $data['password']
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'mobile' => $this->mobile,
            'password' => $this->password
        ];
    }
}