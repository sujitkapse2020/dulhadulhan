<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'sometimes|string|max:100',
            'middle_name' => 'sometimes|nullable|string|max:100',
            'last_name' => 'sometimes|string|max:100',
            'date_of_birth' => 'sometimes|nullable|date',
            'gender' => 'sometimes|in:male,female,other',
            'profile_for' => 'sometimes|in:self,son,daughter,brother,sister,friend,relative,other',
            'age' => 'sometimes|nullable|integer|min:18|max:100',
            'height' => 'sometimes|nullable|integer|min:1|max:300',
            'weight' => 'sometimes|nullable|integer|min:1|max:500',
            'marital_status' => 'sometimes|nullable|string|max:50',
            'mother_tongue' => 'sometimes|nullable|string|max:100',
            'religion_id' => 'sometimes|nullable|integer|exists:religions,id',
            'caste_id' => 'sometimes|nullable|integer|exists:castes,id',
            'sub_caste_id' => 'sometimes|nullable|integer|exists:sub_castes,id',
            'gotra' => 'sometimes|nullable|string|max:100',
            'manglik' => 'sometimes|nullable|string|max:50',
            'blood_group' => 'sometimes|nullable|string|max:10',
            'physical_status' => 'sometimes|nullable|string|max:50',
            'body_type' => 'sometimes|nullable|string|max:50',
            'complexion' => 'sometimes|nullable|string|max:50',
            'about_me' => 'sometimes|nullable|string|max:5000',
            'hobbies' => 'sometimes|nullable|string|max:1000',
            'eating_habits' => 'sometimes|nullable|string|max:50',
            'drinking_habits' => 'sometimes|nullable|string|max:50',
            'smoking_habits' => 'sometimes|nullable|string|max:50',
        ];
    }
}