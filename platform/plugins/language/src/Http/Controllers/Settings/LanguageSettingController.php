<?php

namespace Botble\Language\Http\Controllers\Settings;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Language\Http\Requests\Settings\LanguageSettingRequest;
use Botble\Setting\Http\Controllers\SettingController;

class LanguageSettingController extends SettingController
{
    public function update(LanguageSettingRequest $request): BaseHttpResponse
    {
        return $this->performUpdate([
            ...$request->validated(),
            'language_hide_languages' => $request->input('language_hide_languages', []),
        ]);
    }
}
