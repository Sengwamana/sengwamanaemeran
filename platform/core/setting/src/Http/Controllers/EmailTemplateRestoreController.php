<?php

namespace Botble\Setting\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Setting\Facades\Setting;
use Botble\Setting\Http\Requests\EmailTemplateRestoreRequest;
use Illuminate\Support\Facades\File;

class EmailTemplateRestoreController extends BaseController
{
    public function __invoke(EmailTemplateRestoreRequest $request)
    {
        Setting::delete([$request->input('email_subject_key')]);

        $templatePath = get_setting_email_template_path($request->input('module'), $request->input('template_file'));

        if (File::exists($templatePath)) {
            File::delete($templatePath);
        }

        $shouldBeCleanedDirectories = [
            File::dirname($templatePath),
            storage_path('app/email-templates'),
        ];

        foreach ($shouldBeCleanedDirectories as $shouldBeCleanedDirectory) {
            if (File::isDirectory($shouldBeCleanedDirectory) && File::isEmptyDirectory($shouldBeCleanedDirectory)) {
                File::deleteDirectory($shouldBeCleanedDirectory);
            }
        }

        return $this
            ->httpResponse()
            ->setMessage(trans('core/setting::setting.email.reset_success'));
    }
}
