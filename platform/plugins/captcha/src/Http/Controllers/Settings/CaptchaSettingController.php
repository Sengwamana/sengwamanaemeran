<?php

namespace Botble\Captcha\Http\Controllers\Settings;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Captcha\Forms\CaptchaSettingForm;
use Botble\Captcha\Http\Requests\Settings\CaptchaSettingRequest;
use Botble\Setting\Http\Controllers\SettingController;

class CaptchaSettingController extends SettingController
{
    public function edit()
    {
        $this->pageTitle(trans('plugins/captcha::captcha.settings.title'));

        return CaptchaSettingForm::create()->renderForm();
    }

    public function update(CaptchaSettingRequest $request): BaseHttpResponse
    {
        $request->merge([
            'enable_math_captcha_for_contact_form' => $request->input('enable_math_captcha_botble_contact_forms_fronts_contact_form'),
            'enable_math_captcha_for_newsletter_form' => $request->input('enable_math_captcha_botble_newsletter_forms_fronts_newsletter_form'),
        ]);

        return $this->performUpdate($request->validated());
    }
}
