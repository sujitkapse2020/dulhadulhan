<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyMobileOtpRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [ 'mobile' => ['required', 'string', 'max:30'], 'otp' => ['required', 'digits:6']];
    }
}