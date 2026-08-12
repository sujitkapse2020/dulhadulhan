<?php

namespace App\DTOs;

final class LoginDTO
{
    public function __construct(
        public readonly string $login,
        public readonly string $password,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            login: strtolower(trim($data['login'])),
            password: $data['password'],
        );
    }

    public function toArray(): array
    {
        return [
            'login' => $this->login,
            'password' => $this->password,
        ];
    }
}

