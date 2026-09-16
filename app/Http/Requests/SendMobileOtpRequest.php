<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendMobileOtpRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return ['mobile' => ['required', 'string', 'max:30']];
    }
}