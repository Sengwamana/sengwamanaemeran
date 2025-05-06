@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')

    {!! $form->renderForm() !!}

    <x-core-setting::section
        :title="trans('core/setting::setting.email.email_templates')"
        :description="trans('core/setting::setting.email.email_templates_description')"
        :card="false"
    >
        {!! apply_filters(BASE_FILTER_AFTER_SETTING_EMAIL_CONTENT, null) !!}
    </x-core-setting::section>
@stop
