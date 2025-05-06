<?php

namespace Botble\Theme\Http\Controllers;

use Botble\Setting\Http\Controllers\SettingController;
use Botble\Theme\Forms\Settings\WebsiteTrackingSettingForm;
use Botble\Theme\Http\Requests\WebsiteTrackingSettingRequest;

class WebsiteTrackingSettingController extends SettingController
{
    public function edit()
    {
        $this->pageTitle(trans('packages/theme::theme.settings.website_tracking.title'));

        return WebsiteTrackingSettingForm::create()->renderForm();
    }

    public function update(WebsiteTrackingSettingRequest $request)
    {
        return $this->performUpdate(
            $request->validated()
        )->withUpdatedSuccessMessage();
    }
}
