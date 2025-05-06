<?php

namespace Botble\Captcha\Http\Requests\Settings;

use Botble\Base\Rules\OnOffRule;
use Botble\Captcha\Facades\Captcha;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class CaptchaSettingRequest extends Request
{
    public function rules(): array
    {
        $rules = [
            'enable_captcha' => $onOffRule = new OnOffRule(),
            'captcha_type' => ['nullable', 'in:v2,v3', $enableCaptchaRule = 'required_if:enable_captcha,1'],
            'captcha_hide_badge' => $onOffRule,
            'captcha_show_disclaimer' => $onOffRule,
            'captcha_site_key' => ['nullable', 'string', $enableCaptchaRule],
            'captcha_secret' => ['nullable', 'string', $enableCaptchaRule],
            'enable_math_captcha' => $onOffRule,
            'recaptcha_score' => ['nullable', Rule::in(Captcha::scores()), 'required_if:captcha_type,v3'],
        ];

        $rules = [
            ...$rules,
            ...$this->formSelectorRules('enable_math_captcha'),
            ...$this->formSelectorRules('enable_recaptcha'),
        ];

        return apply_filters('captcha_settings_validation_rules', $rules);
    }

    protected function formSelectorRules(string $key): array
    {
        $rules = [];

        foreach (array_keys(Captcha::getFormsSupport()) as $form) {
            $rules[Captcha::formSettingKey($form, $key)] = new OnOffRule();
        }

        return $rules;
    }
}
