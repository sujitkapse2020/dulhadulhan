<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class UserPhotoRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $photo = $this->file('photo');

        if ($photo instanceof UploadedFile) {
            $this->merge(['photo' => [$photo]]);
        }
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'photo' => ['required', 'array', 'max:10'],
            'photo.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }
}