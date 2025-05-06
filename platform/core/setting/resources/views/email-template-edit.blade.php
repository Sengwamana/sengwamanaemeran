@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <x-core::form
        :url="$updateUrl"
        method="put"
    >
        <input
            type="hidden"
            name="module"
            value="{{ $pluginData['name'] }}"
        >
        <input
            type="hidden"
            name="template_file"
            value="{{ $pluginData['template_file'] }}"
        >

        <x-core-setting::section
            :title="trans('core/setting::setting.email.title')"
            :description="trans('core/setting::setting.email.description')"
        >
            @if ($emailSubject)
                <input
                    type="hidden"
                    name="email_subject_key"
                    value="{{ get_setting_email_subject_key($pluginData['type'], $pluginData['name'], $pluginData['template_file']) }}"
                >

                <x-core::form.text-input
                    name="email_subject"
                    :label="trans('core/setting::setting.email.subject')"
                    :value="$emailSubject"
                    data-counter="300"
                />
            @endif

            <x-core::form-group>
                <x-core::form.label for="mail-template-editor">
                    {{ trans('core/setting::setting.email.content') }}
                </x-core::form.label>

                <x-core::twig-editor
                    :variables="EmailHandler::getVariables($pluginData['type'], $pluginData['name'], $pluginData['template_file'])"
                    :functions="EmailHandler::getFunctions()"
                    :value="$emailContent"
                    name="email_content"
                    mode="html"
                >
                </x-core::twig-editor>
            </x-core::form-group>

            @if (
                $metabox = apply_filters(
                    'setting_email_template_meta_boxes',
                    null,
                    request()->route()->parameters()))
                <x-slot:footer>
                    <div class="mt-3">
                        {!! $metabox !!}
                    </div>
                </x-slot:footer>
            @endif
        </x-core-setting::section>

        <x-core-setting::section.action>
            <div class="btn-list">
                <x-core::button
                    type="submit"
                    color="primary"
                    icon="ti ti-device-floppy"
                >
                    {{ trans('core/setting::setting.save_settings') }}
                </x-core::button>
                <x-core::button
                    tag="a"
                    href="{{ $restoreUrl . BaseHelper::stringify(request()->input('ref_lang')) }}"
                    icon="ti ti-arrow-back-up"
                    data-bb-toggle="reset-default"
                >
                    {{ trans('core/setting::setting.email.reset_to_default') }}
                </x-core::button>
                <x-core::button
                    tag="a"
                    href="{{ route('settings.email.template.preview', ['type' => $pluginData['type'], 'module' => $pluginData['name'], 'template' => $pluginData['template_file'], 'ref_lang' => request()->input('ref_lang')]) }}"
                    target="_blank"
                    icon="ti ti-eye"
                >
                    {{ trans('core/setting::setting.preview') }}
                </x-core::button>
            </div>
        </x-core-setting::section.action>
    </x-core::form>

    <x-core-setting::section class="mt-6">
        <h4>{{ trans('core/base::base.email_template.icon_variables') }}</h4>

        @if (! empty($iconVariables = EmailHandler::getIconVariables()))
            <x-core::table>
                <x-core::table.header>
                    <x-core::table.header.cell width="50">
                        {{ trans('core/base::base.email_template.preview')  }}
                    </x-core::table.header.cell>

                    <x-core::table.header.cell>
                        {{ trans('core/base::base.email_template.variable')  }}
                    </x-core::table.header.cell>
                </x-core::table.header>
                <x-core::table.body>
                    @foreach (EmailHandler::getIconVariables() as $key => $value)
                        <x-core::table.body.row>
                            <x-core::table.body.cell>
                                <div style="background-color: #206bc4; border-radius: 50%; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center">
                                    <img
                                        src="{{ $value }}"
                                        width="32"
                                        height="32"
                                        alt="{{ $key }}"
                                    />
                                </div>
                            </x-core::table.body.cell>

                            <x-core::table.body.cell>
                                <span class="text-danger">&#123;&#123; '{{ $key }}' | icon_url &#125;&#125;</span>
                                <a
                                    href="javascript:void(0);"
                                    data-bb-toggle="clipboard"
                                    data-clipboard-action="copy"
                                    data-clipboard-text="&#123;&#123; '{{ $key }}' | icon_url &#125;&#125;"
                                    data-clipboard-message="{{ trans('core/table::table.copied') }}"
                                    data-bs-toggle="tooltip"
                                    class="text-muted text-center text-decoration-none ms-1"
                                >
                                    <span class="sr-only">{{ trans('core/table::table.copy') }}</span>
                                    <x-core::icon name="ti ti-clipboard" />
                                </a>

                            </x-core::table.body.cell>
                        </x-core::table.body.row>
                    @endforeach
                </x-core::table.body>
            </x-core::table>
        @else
            <x-core::alert type="warning" class="mt-4" :title="trans('core/base::base.email_template.missing_icons')">
                <p class="mt-2">
                    {!! BaseHelper::clean(trans('core/base::base.email_template.missing_icons_description', [
                       'from' => Html::tag('code', 'platform/core/base/public/images/email-icons'),
                       'to' => Html::tag('code', 'public/vendor/core/core/base/images/email-icons'),
                    ])) !!}
                </p>
            </x-core::alert>
        @endif

        <x-core::alert class="mt-4" :title="trans('core/base::base.email_template.usage')">
            <ul class="mt-2">
                <li>
                    <p>
                        {!! BaseHelper::clean(trans('core/base::base.email_template.icon_variable_usage_description', [
                            'variable' => Html::tag('code', '&#123;&#123 \'example\' | icon_url &#125;&#125;'),
                        ])) !!}
                    </p>
                    <pre><code>&lt;img src="&#123;&#123 'example' | icon_url &#125;&#125;" class="bb-va-middle" width="40" height="40" alt="Icon"&gt;</code></pre>
                </li>
                <li>
                    {!! BaseHelper::clean(trans(
                        'core/base::base.email_template.add_more_icon_description',
                        ['path' => Html::tag('code', 'public/vendor/core/core/base/images/email-icons')]
                    )) !!}
                </li>
            </ul>
        </x-core::alert>
    </x-core-setting::section>

    <x-core::modal.action
        id="reset-template-to-default-modal"
        type="warning"
        :title="trans('core/setting::setting.email.confirm_reset')"
        :description="trans('core/setting::setting.email.confirm_message')"
        :submit-button-attrs="['id' => 'reset-template-to-default-button']"
        :submit-button-label="trans('core/setting::setting.email.continue')"
    />
@stop
