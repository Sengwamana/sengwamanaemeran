<?php

namespace Botble\Setting\Http\Controllers;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Setting\Forms\DataTableSettingForm;
use Botble\Setting\Http\Requests\DataTableSettingRequest;

class DataTableSettingController extends SettingController
{
    public function edit()
    {
        $this->pageTitle(trans('core/setting::setting.datatable.title'));

        return DataTableSettingForm::create()->renderForm();
    }

    public function update(DataTableSettingRequest $request): BaseHttpResponse
    {
        if (setting('datatables_default_show_column_visibility')
            != $request->input('datatables_default_show_column_visibility')) {
            setting()->set('datatables_random_hash', hash('sha256', (string) time()));
        }

        return $this->performUpdate($request->validated());
    }
}
