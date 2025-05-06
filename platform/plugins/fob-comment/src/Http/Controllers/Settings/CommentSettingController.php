<?php

namespace FriendsOfBotble\Comment\Http\Controllers\Settings;

use Botble\Setting\Http\Controllers\SettingController;
use FriendsOfBotble\Comment\Forms\Settings\CommentSettingForm;
use FriendsOfBotble\Comment\Http\Requests\Settings\CommentSettingRequest;

class CommentSettingController extends SettingController
{
    public function edit()
    {
        $this->pageTitle(trans('plugins/fob-comment::comment.settings.title'));

        return CommentSettingForm::create()->renderForm();
    }

    public function update(CommentSettingRequest $request)
    {
        return $this->performUpdate($request->validated());
    }
}
