<?php

namespace Botble\Contact\Http\Controllers\Settings;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Contact\Forms\Settings\ContactSettingForm;
use Botble\Contact\Http\Requests\Settings\ContactSettingRequest;
use Botble\Setting\Http\Controllers\SettingController;
use Illuminate\Support\Arr;

class ContactSettingController extends SettingController
{
    public function edit()
    {
        $this->pageTitle(trans('plugins/contact::contact.settings.title'));

        return ContactSettingForm::create()->renderForm();
    }

    public function update(ContactSettingRequest $request): BaseHttpResponse
    {
        return $this->performUpdate(Arr::except($request->validated(), [
            'receiver_emails_for_validation',
            'blacklist_keywords_for_validation',
        ]));
    }
}
