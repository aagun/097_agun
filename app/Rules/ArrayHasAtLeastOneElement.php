<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ArrayHasAtLeastOneElement implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (is_array($value) && count($value) < 1) {
            $fail('validation.custom.array_has_at_least_one_element')
            ->translate([
                'attribute' => $attribute
            ]);
        }
    }

}
