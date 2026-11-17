<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

/**
 * Strict Philippine mobile number: exactly 11 digits, numbers only, starting with 09
 * (e.g. 09171234567). No spaces, dashes, brackets or +63 prefix.
 *
 * Used by every phone field, back office and storefront. The frontend `<Input type="tel">`
 * filters keystrokes to the same shape (see resources/js/components/ui/input.tsx).
 */
class PhilippinePhone implements ValidationRule
{
    public const PATTERN = '/^09\d{9}$/';

    /**
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || ! ctype_digit($value)) {
            $fail('The :attribute must contain numbers only.');

            return;
        }

        if (strlen($value) !== 11) {
            $fail('The :attribute must be exactly 11 digits.');

            return;
        }

        if (! preg_match(self::PATTERN, $value)) {
            $fail('The :attribute must start with 09 (e.g. 09171234567).');
        }
    }
}
