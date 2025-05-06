<?php

namespace Botble\Setting\Http\Requests;

use Botble\Base\Rules\EmailRule;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class EmailTemplateSettingRequest extends Request
{
    public function prepareForValidation(): void
    {
        if ($this->input('email_template_social_links') == '[]') {
            $this->merge([
                'email_template_social_links' => null,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'email_template_logo' => ['nullable', 'string'],
            'email_template_email_contact' => ['nullable', new EmailRule()],
            'email_template_social_links' => ['nullable', 'array'],
            'email_template_social_links.*.*.value' => ['nullable', 'string'],
            'email_template_social_links.*.*.key' => ['nullable', 'string', Rule::in(['name', 'url', 'image', ])],
            'email_template_copyright_text' => ['nullable', 'string'],
            'email_template_custom_css' => ['nullable', 'string', 'max:100000'],
            'email_template_max_height_for_logo' => ['nullable', 'integer', 'min:1', 'max:150'],
        ];
    }
}
