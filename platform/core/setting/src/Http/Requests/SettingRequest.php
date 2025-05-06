<?php

namespace Botble\Setting\Http\Requests;

use Botble\Base\Facades\BaseHelper;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class SettingRequest extends Request
{
    public function rules(): array
    {
        return apply_filters('cms_settings_validation_rules', [
            'admin_logo' => ['nullable', 'string', 'max:255'],
            'admin_favicon' => ['nullable', 'string', 'max:255'],
            'login_screen_backgrounds' => ['nullable', 'array'],
            'login_screen_backgrounds.*' => ['nullable', 'string', 'max:255'],
            'admin_title' => ['nullable', 'string', 'max:255'],
            'rich_editor' => ['required', Rule::in(array_keys(BaseHelper::availableRichEditors()))],
        ]);
    }
}
