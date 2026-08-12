<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'first_name' => 'required|string|max:100',

            'last_name' => 'required|string|max:100',

            'email' => 'required|email|unique:users,email',

            'mobile' => 'required|digits:10|unique:users,mobile',

            'password' => [
                'required',
                Password::min(6)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],

            'date_of_birth' => 'required|date',

            'gender' => 'required|in:male,female,other',

            'terms_accepted' => "required|in:accepted",
            
            "profile_for" => "required|in:self,son,daughter,brother,sister,friend,relative,other"
        ];
    }
}