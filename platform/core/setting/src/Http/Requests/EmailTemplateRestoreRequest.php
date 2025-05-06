<?php

namespace Botble\Setting\Http\Requests;

use Botble\Support\Http\Requests\Request;

class EmailTemplateRestoreRequest extends Request
{
    public function rules(): array
    {
        return [
            'module' => ['required', 'string', 'alpha_dash'],
            'template_file' => ['required', 'string', 'alpha_dash'],
        ];
    }
}
