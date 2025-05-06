<?php

namespace Botble\Optimize\Http\Controllers\Settings;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Optimize\Forms\Settings\OptimizeSettingForm;
use Botble\Optimize\Http\Requests\OptimizeSettingRequest;
use Botble\Setting\Http\Controllers\SettingController;

class OptimizeSettingController extends SettingController
{
    public function edit()
    {
        $this->pageTitle(trans('packages/optimize::optimize.settings.title'));

        return OptimizeSettingForm::create()->renderForm();
    }

    public function update(OptimizeSettingRequest $request): BaseHttpResponse
    {
        return $this->performUpdate($request->validated());
    }
}
