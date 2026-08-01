<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhilippinePhone implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail('The :attribute must be a valid phone string.');
            return;
        }

        // Clean spaces, hyphens, and parentheses
        $cleaned = preg_replace('/[\s\-\(\)]+/', '', $value);

        // Pattern matches:
        // 09XXXXXXXXX (11 digits starting with 09)
        // +639XXXXXXXXX (13 chars starting with +639)
        // 639XXXXXXXXX (12 digits starting with 639)
        $pattern = '/^(09|\+639|639)\d{9}$/';

        if (! preg_match($pattern, $cleaned)) {
            $fail('The :attribute must be a valid Philippine mobile number (e.g. 09171234567 or +639171234567).');
        }
    }
}
