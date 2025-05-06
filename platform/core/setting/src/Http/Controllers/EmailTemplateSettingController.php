<?php

namespace Botble\Setting\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Setting\Forms\EmailTemplateSettingForm;
use Botble\Setting\Http\Requests\EmailTemplateSettingRequest;
use Illuminate\Contracts\View\View;

class EmailTemplateSettingController extends SettingController
{
    public function index(): View
    {
        $this->pageTitle(trans('core/setting::setting.email.email_templates'));

        Assets::addScriptsDirectly('vendor/core/core/setting/js/email-template.js');

        $form = EmailTemplateSettingForm::create();

        return view('core/setting::email-templates', compact('form'));
    }

    public function update(EmailTemplateSettingRequest $request): BaseHttpResponse
    {
        return $this->performUpdate(
            $request->validated()
        )->withUpdatedSuccessMessage();
    }
}
