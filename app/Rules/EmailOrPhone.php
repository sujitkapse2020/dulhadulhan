<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class EmailOrPhone implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $value = trim($value);

        // If input contains '@', treat it as an email
        if (str_contains($value, '@')) {

            if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $fail('Please enter a valid email address.');
            }

            return;
        }

        // Otherwise, treat it as a phone number
        if (! preg_match('/^[6-9]\d{9}$/', $value)) {
            $fail('Please enter a valid 10-digit mobile number.');
        }
    }
}
