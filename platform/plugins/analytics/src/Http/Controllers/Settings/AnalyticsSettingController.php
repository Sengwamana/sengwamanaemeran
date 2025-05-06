<?php

namespace Botble\Analytics\Http\Controllers\Settings;

use Botble\Analytics\Forms\AnalyticsSettingForm;
use Botble\Analytics\Http\Requests\Settings\AnalyticsSettingRequest;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Setting\Http\Controllers\SettingController;

class AnalyticsSettingController extends SettingController
{
    public function edit()
    {
        $this->pageTitle(trans('plugins/analytics::analytics.settings.title'));

        return AnalyticsSettingForm::create()->renderForm();
    }

    public function update(AnalyticsSettingRequest $request): BaseHttpResponse
    {
        return $this->performUpdate($request->validated());
    }
}
