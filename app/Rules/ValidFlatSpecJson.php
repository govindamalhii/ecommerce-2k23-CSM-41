<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Enforces the Sprint 2 specification rule: `products.specifications` must
 * be a flat object (no nested arrays/objects), max 20 keys, snake_case keys,
 * scalar values only. MySQL's JSON column only guarantees valid JSON syntax,
 * not this shape, so it has to be checked here as well as in the DB.
 */
class ValidFlatSpecJson implements ValidationRule
{
    private const MAX_KEYS = 20;

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_array($value)) {
            $fail('The :attribute must be a flat JSON object.');
            return;
        }

        if (count($value) > self::MAX_KEYS) {
            $fail('The :attribute may not have more than ' . self::MAX_KEYS . ' keys.');
            return;
        }

        foreach ($value as $key => $val) {
            if (! is_string($key) || ! preg_match('/^[a-z][a-z0-9_]*$/', $key)) {
                $fail("The :attribute key \"{$key}\" must be a snake_case string.");
                return;
            }

            if (is_array($val) || is_object($val)) {
                $fail("The :attribute value for \"{$key}\" must be a string, number, or boolean — nested structures aren't allowed.");
                return;
            }
        }
    }
}
