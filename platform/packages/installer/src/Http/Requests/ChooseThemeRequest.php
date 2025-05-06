<?php

namespace Botble\Installer\Http\Requests;

use Botble\Installer\InstallerStep\InstallerStep;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class ChooseThemeRequest extends Request
{
    public function rules(): array
    {
        return [
            'theme' => ['required', 'string', Rule::in(array_keys(InstallerStep::getThemes()))],
        ];
    }
}
