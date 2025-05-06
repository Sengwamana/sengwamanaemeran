<?php

namespace ArchiElite\Announcement\Http\Controllers;

use ArchiElite\Announcement\Http\Requests\SettingRequest;
use Botble\Base\Facades\PageTitle;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Setting\Facades\Setting;
use Illuminate\Contracts\View\View;

class SettingController extends BaseController
{
    public function index(): View
    {
        PageTitle::setTitle(trans('plugins/announcement::announcements.settings.name'));

        return view('plugins/announcement::settings');
    }

    public function update(SettingRequest $request, BaseHttpResponse $response): BaseHttpResponse
    {
        Setting::set($request->validated());
        Setting::save();

        return $response
            ->withUpdatedSuccessMessage();
    }
}
