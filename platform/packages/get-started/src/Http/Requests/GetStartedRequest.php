<?php

namespace Botble\GetStarted\Http\Requests;

use Botble\Base\Rules\EmailRule;
use Botble\Support\Http\Requests\Request;

class GetStartedRequest extends Request
{
    public function rules(): array
    {
        return [
            'step' => ['required', 'numeric', 'min:1', 'max:4'],
            'username' => ['required_if:step,3', 'max:30', 'min:4'],
            'email' => ['required_if:step,3', 'max:60', 'min:6', new EmailRule()],
            'password' => ['required_if:step,3', 'min:6', 'max:60'],
            'password_confirmation' => ['required_if:step,3', 'same:password'],
        ];
    }
}
