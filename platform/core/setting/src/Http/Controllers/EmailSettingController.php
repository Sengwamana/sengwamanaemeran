<?php

namespace Botble\Setting\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Setting\Forms\EmailSettingForm;
use Botble\Setting\Http\Requests\EmailSettingRequest;

class EmailSettingController extends SettingController
{
    public function edit()
    {
        $this->pageTitle(trans('core/setting::setting.panel.email'));

        Assets::addScriptsDirectly('vendor/core/core/setting/js/email-template.js');

        $form = null;

        if (config('core.base.general.enable_email_configuration_from_admin_panel', true)) {
            $form = EmailSettingForm::create();
        }

        return view('core/setting::email', compact('form'));
    }

    public function update(EmailSettingRequest $request): BaseHttpResponse
    {
        return $this->performUpdate($request->validated());
    }
}
