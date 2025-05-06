<?php

namespace ArchiElite\Announcement\Http\Controllers\Settings;

use ArchiElite\Announcement\Forms\Settings\AnnouncementSettingForm;
use ArchiElite\Announcement\Http\Requests\Settings\AnnouncementSettingRequest;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Setting\Http\Controllers\SettingController;

class AnnouncementSettingController extends SettingController
{
    public function edit(FormBuilder $formBuilder)
    {
        $this->pageTitle(trans('plugins/announcement::announcements.settings.name'));

        return AnnouncementSettingForm::create()->renderForm();
    }

    public function update(AnnouncementSettingRequest $request): BaseHttpResponse
    {
        return $this->performUpdate($request->validated());
    }
}
