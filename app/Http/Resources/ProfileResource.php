<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'gender' => $this->gender,
            'dob' => $this->dob?->toDateString(),
            'profile_for' => $this->profile_for,
            'age' => $this->age,
            'height' => $this->height,
            'weight' => $this->weight,
            'marital_status' => $this->marital_status,
            'mother_tongue' => $this->mother_tongue,
            'religion_id' => $this->religion_id,
            'caste_id' => $this->caste_id,
            'sub_caste_id' => $this->sub_caste_id,
            'gotra' => $this->gotra,
            'manglik' => $this->manglik,
            'blood_group' => $this->blood_group,
            'physical_status' => $this->physical_status,
            'body_type' => $this->body_type,
            'complexion' => $this->complexion,
            'about_me' => $this->about_me,
            'hobbies' => $this->hobbies,
            'eating_habits' => $this->eating_habits,
            'drinking_habits' => $this->drinking_habits,
            'smoking_habits' => $this->smoking_habits,
        ];
    }
}