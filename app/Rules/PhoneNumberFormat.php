<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\ValidationRule;
use Closure;

class PhoneNumberFormat implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! preg_match('/^\+?[0-9]{8,15}$/', $value)) {
            $fail('Le format du numéro de téléphone est invalide.');
        }
    }
}
