<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CaseInsensitiveOrder implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!in_array(strtoupper($value), ['ASC', 'DESC'])) {
            $fail('validation.custom.case_insensitive_order')
                ->translate([
                    'attribute' => $attribute
                ]);
        }
    }
}
