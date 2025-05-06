<?php

namespace Botble\Faq\Http\Controllers\Settings;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Faq\Forms\Settings\FaqSettingForm;
use Botble\Faq\Http\Requests\Settings\FaqSettingRequest;
use Botble\Setting\Http\Controllers\SettingController;

class FaqSettingController extends SettingController
{
    public function edit()
    {
        $this->pageTitle(trans('plugins/faq::faq.settings.title'));

        return FaqSettingForm::create()->renderForm();
    }

    public function update(FaqSettingRequest $request): BaseHttpResponse
    {
        return $this->performUpdate($request->validated());
    }
}
