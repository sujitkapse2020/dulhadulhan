<?php

namespace App\DTOs;

final class UpdateProfileDTO
{
    public function __construct(
        public readonly array $attributes,
    ) {}

    public static function fromArray(array $data): self
    {
        $attributes = array_intersect_key($data, array_flip([
            'first_name', 'middle_name', 'last_name', 'date_of_birth', 'gender', 'profile_for',
            'age', 'height', 'weight', 'marital_status', 'mother_tongue', 'religion_id', 'caste_id',
            'sub_caste_id', 'gotra', 'manglik', 'blood_group', 'physical_status', 'body_type',
            'complexion', 'about_me', 'hobbies', 'eating_habits', 'drinking_habits', 'smoking_habits',
        ]));

        if (array_key_exists('date_of_birth', $attributes)) {
            $attributes['dob'] = $attributes['date_of_birth'];
            unset($attributes['date_of_birth']);
        }

        return new self($attributes);
    }
}