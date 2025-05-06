@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    @if ($form)
        {!! $form->renderForm() !!}
    @endif

    <div class="mt-5">
        <x-core-setting::section
            :title="trans('core/setting::setting.email.email_template_status')"
            :description="trans('core/setting::setting.email.email_template_status_description')"
            :card="false"
        >
            @foreach (EmailHandler::getTemplates() as $type => $item)
                @foreach ($item as $module => $data)
                    @include('core/setting::template-on-off', compact('type', 'module', 'data'))
                @endforeach
            @endforeach
        </x-core-setting::section>
    </div>
@stop

@push('footer')
    <x-core::modal
        id="send-test-email-modal"
        :title="trans('core/setting::setting.test_email_modal_title')"
        type="info"
    >
        <x-core::form.text-input
            :label="trans('core/setting::setting.test_email_description')"
            type="email"
            name="email"
            :placeholder="trans('core/setting::setting.test_email_input_placeholder')"
        />

        @php
            $emailTemplates = [
                '' => trans('core/setting::setting.test_email_template'),
            ];

            foreach (EmailHandler::getTemplates() as $type => $item) {
                foreach ($item as $module => $data) {
                    foreach ($data['templates'] as $key => $template) {
                        $emailTemplates[$type . '.' . $module . '.' . $key] = trans($template['title']);
                    }
                }
            }
        @endphp

        <x-core::form.select
            :label="trans('core/setting::setting.select_email_template')"
            type="template"
            name="template"
            :options="$emailTemplates"
        />

        <x-slot:footer>
            <div class="w-100 row">
                <div class="col">
                    <x-core::button
                        data-bs-dismiss="modal"
                        class="w-100"
                    >
                        {{ __('Close') }}
                    </x-core::button>
                </div>
                <div class="col">
                    <x-core::button
                        color="primary"
                        id="send-test-email-btn"
                        data-url="{{ route('settings.email.test.send') }}"
                        class="w-100"
                    >
                        {{ trans('core/setting::setting.send') }}
                    </x-core::button>
                </div>
            </div>
        </x-slot:footer>
    </x-core::modal>
@endpush
