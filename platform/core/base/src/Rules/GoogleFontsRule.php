<?php

namespace Botble\Base\Rules;

use Botble\Base\Facades\BaseHelper;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class GoogleFontsRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $message = __('The selected :attribute is invalid.', compact('attribute'));

        if ($value === null || $value === '') {
            $fail($message);
        }

        if (! in_array($value, BaseHelper::getFonts())) {
            $fail($message);
        }
    }
}
