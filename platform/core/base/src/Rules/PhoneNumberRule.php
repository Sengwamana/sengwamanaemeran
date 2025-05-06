<?php

namespace Botble\Base\Rules;

use Botble\Base\Facades\BaseHelper;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneNumberRule implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $rules = BaseHelper::getPhoneValidationRule(asArray: true);

        $min = str($rules[0])->replace('min:', '')->toInteger();
        $max = str($rules[1])->replace('max:', '')->toInteger();
        $regex = str($rules[2])->replace('regex:', '');

        $length = mb_strlen($value);

        if ($length < $min) {
            $fail(trans('validation.min.string', ['min' => $min]));
        }

        if ($length > $max) {
            $fail(trans('validation.max.string', ['max' => $max]));
        }

        if (preg_match($regex, $value) == false) {
            $fail(trans('validation.regex'));
        }
    }
}
