<?php

namespace Botble\Base\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class OnOffRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $message = __('The selected :attribute is invalid.', compact('attribute'));

        if ($value === null || $value === '') {
            $fail($message);
        }

        if (! is_string($value) && ! is_numeric($value)) {
            $fail($message);
        }

        if (! in_array((string) $value, ['0', '1'], true)) {
            $fail($message);
        }
    }
}
