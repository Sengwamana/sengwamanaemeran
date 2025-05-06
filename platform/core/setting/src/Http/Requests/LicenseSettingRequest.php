<?php

namespace Botble\Setting\Http\Requests;

use Botble\Support\Http\Requests\Request;

class LicenseSettingRequest extends Request
{
    public function rules(): array
    {
        return [
            'purchase_code' => ['required', 'string', 'min:19', 'max:36', 'regex:/^[\pL\s\ \_\-0-9]+$/u'],
            'buyer' => ['required', 'string', 'min:2', 'max:60'],
            'license_rules_agreement' => ['accepted:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'license_rules_agreement.accepted' => 'Please agree to the license terms by clicking on the checkbox labeled "Confirm that I agree to the Envato License Terms..."',
        ];
    }
}
