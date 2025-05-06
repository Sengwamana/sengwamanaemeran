<?php

namespace Botble\Portfolio\Http\Requests;

use Botble\Captcha\Facades\Captcha;
use Botble\Portfolio\Enums\CustomFieldType;
use Botble\Portfolio\Models\CustomField;
use Botble\Support\Http\Requests\Request;

class QuoteRequest extends Request
{
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:10000'],
            'custom_fields' => ['nullable', 'array'],
        ];

        if (is_plugin_active('captcha') && Captcha::reCaptchaEnabled()) {
            $rules += Captcha::rules();
        }

        $customFields = CustomField::query()
            ->with('options:id,custom_field_id,label')
            ->get();

        $customFields->each(function (CustomField $customField) use (&$rules): void {
            $customFieldRules[] = $customField->required ? 'required' : 'nullable';

            if ($customField->type == CustomFieldType::DROPDOWN) {
                $customFieldRules[] = 'in:' . $customField->options->pluck('id')->implode(',');
            }

            $rules["custom_fields.{$customField->getKey()}"] = $customFieldRules;
        });

        return $rules;
    }

    public function attributes(): array
    {
        $attributes = is_plugin_active('captcha') ? Captcha::attributes() : [];

        CustomField::query()
            ->wherePublished()
            ->pluck('name', 'id')
            ->each(function (string $name, string $id) use (&$attributes) {
                return $attributes["custom_fields.$id"] = strtolower($name);
            });

        return $attributes;
    }
}
