<?php

namespace App\DTOs;

use Carbon\Carbon;

final class UserProfileDTO
{
    public function __construct(
        public readonly string $firstName,
        public readonly string $lastName,  
        public readonly string $gender,    
        public readonly string $profileFor, 
        public readonly Carbon $dateOfBirth        
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            firstName: $data['first_name'],
            lastName: $data['last_name'],
            gender: $data['gender'],
            profileFor: $data['profile_for'],
            dateOfBirth: Carbon::parse($data['date_of_birth'])
        );
    }

    public function toArray(): array
    {
        return [
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'date_of_birth' => $this->dateOfBirth->toDateString(),
            'gender' => $this->gender,
            'profile_for' => $this->profileFor,
        ];
    }
}