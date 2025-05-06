<x-core::modal
    class="get-started-modal"
    size="lg"
    data-step="1"
    :close-button="false"
    :form-action="route('get-started.save')"
    data-bs-backdrop="static"
>
    <x-core::modal.close-button />

    <div class="get-start-wrapper text-center">
        <div class="mb-5">
            <x-core::icon name="ti ti-confetti" class="get-start-icon" />
        </div>

        <h4 class="lh-base">{{ trans('packages/get-started::get-started.welcome_title') }}</h4>

        <div class="text-muted">{{ trans('packages/get-started::get-started.welcome_description') }}</div>

        <div class="mt-5">
            <input
                type="hidden"
                name="step"
                value="1"
            >
            <x-core::button
                type="submit"
                color="primary"
            >
                {{ trans('packages/get-started::get-started.get_started') }}
            </x-core::button>
        </div>
    </div>
</x-core::modal>

<x-core::modal
    class="get-started-modal"
    size="lg"
    data-step="2"
    :title="trans('packages/get-started::get-started.customize_branding_title')"
    :form-action="route('get-started.save')"
    data-bs-backdrop="static"
>
    <p>{{ trans('packages/get-started::get-started.customize_branding_description') }}</p>

    <div class="get-start-wrapper">
        <input
            type="hidden"
            name="step"
            value="2"
        >
        <div
            class="select-colors-fonts"
            data-select2-dropdown-parent="true"
        >
            <div class="row">
                @if (ThemeOption::hasField('primary_color'))
                    <div class="col-sm-6">
                        <h6>{{ trans('packages/get-started::get-started.colors') }}</h6>

                        <div class="mb-3">
                            <x-core::form.label
                                for="primary_color"
                                :label="trans('packages/get-started::get-started.primary_color')"
                            />
                            {!! ThemeOption::renderField(theme_option()->getField('primary_color')) !!}
                        </div>
                    </div>
                @endif

                @if (ThemeOption::hasField('primary_font'))
                    <div class="col-sm-6">
                        <h6>{{ trans('packages/get-started::get-started.fonts') }}</h6>
                        <div class="mb-3">
                            <x-core::form.label
                                for="primary_font"
                                :label="trans('packages/get-started::get-started.primary_font')"
                            />
                            {!! ThemeOption::renderField(ThemeOption::getField('primary_font')) !!}
                        </div>
                    </div>
                @endif
            </div>
            <br>
            <div class="row">
                <div class="col-sm-12">
                    <h6>{{ trans('packages/get-started::get-started.identify') }}</h6>
                    <x-core::form.text-input
                        name="site_title"
                        :label="trans('packages/get-started::get-started.site_title')"
                        :value="theme_option('site_title')"
                        :placeholder="trans('packages/get-started::get-started.site_title')"
                    />
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="mb-3">
                        <x-core::form.label
                            for="site-logo"
                            :label="trans('packages/get-started::get-started.logo')"
                        />
                        {!! Form::mediaImage('logo', Theme::getLogo(), ['allow_thumb' => false]) !!}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="mb-3">
                        <x-core::form.label
                            for="site-favicon"
                            :label="trans('packages/get-started::get-started.favicon')"
                        />
                        {!! Form::mediaImage('favicon', theme_option('favicon'), ['allow_thumb' => false]) !!}
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="mb-3">
                        <x-core::form.label
                            for="admin-logo"
                            :label="trans('packages/get-started::get-started.admin_logo')"
                        />
                        {!! Form::mediaImage('admin_logo', setting('admin_logo'), [
                            'allow_thumb' => false,
                            'default_image' => url(config('core.base.general.logo')),
                        ]) !!}
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="mb-3">
                        <x-core::form.label
                            for="admin-favicon"
                            :label="trans('packages/get-started::get-started.admin_favicon')"
                        />
                        {!! Form::mediaImage('admin_favicon', setting('admin_favicon'), [
                            'allow_thumb' => false,
                            'default_image' => url(config('core.base.general.favicon')),
                        ]) !!}
                    </div>
                </div>
            </div>
        </div>

        <x-core::button
            type="submit"
            color="primary"
            icon="ti ti-chevrons-right"
            icon-position="right"
        >
            {{ trans('packages/get-started::get-started.next_step') }}
        </x-core::button>
    </div>
</x-core::modal>

<x-core::modal
    class="get-started-modal"
    size="lg"
    data-step="3"
    :title="trans('packages/get-started::get-started.change_default_account_info_title')"
    :form-action="route('get-started.save')"
    data-bs-backdrop="static"
>
    <p>{{ trans('packages/get-started::get-started.change_default_account_info_description') }}</p>

    <div
        class="get-start-wrapper"
        style="min-height: 0"
    >
        <input
            type="hidden"
            name="step"
            value="3"
        >
        <x-core::form.text-input
            name="username"
            :label="trans('packages/get-started::get-started.username')"
            :value="auth()->guard()->user()->username"
        />

        <x-core::form.text-input
            name="email"
            type="email"
            :label="trans('packages/get-started::get-started.email')"
            :value="auth()->guard()->user()->email"
        />

        <div class="row">
            <div class="col-sm-6">
                <x-core::form.text-input
                    type="password"
                    name="password"
                    :label="trans('packages/get-started::get-started.password')"
                />
            </div>
            <div class="col-sm-6">
                <x-core::form.text-input
                    type="password"
                    name="password_confirmation"
                    :label="trans('packages/get-started::get-started.password_confirmation')"
                />
            </div>
        </div>

        <x-core::button
            type="submit"
            color="primary"
            icon="ti ti-chevrons-right"
            icon-position="right"
        >
            {{ trans('packages/get-started::get-started.next_step') }}
        </x-core::button>
    </div>
</x-core::modal>

<x-core::modal
    class="get-started-modal"
    size="lg"
    data-step="4"
    :close-button="false"
    :form-action="route('get-started.save')"
    data-bs-backdrop="static"
>
    <x-core::modal.close-button />

    <div
        class="get-start-wrapper text-center"
        style="min-height: 0"
    >
        <div class="mb-5">
            <x-core::icon
                name="ti ti-circle-check"
                class="text-success icon-lg"
            />
        </div>

        <h4 class="text-center">{{ trans('packages/get-started::get-started.site_ready_title') }}</h4>
        <div class="text-muted">{{ trans('packages/get-started::get-started.site_ready_description') }}</div>

        <div class="mt-6">
            <input
                type="hidden"
                name="step"
                value="4"
            >

            <x-core::button
                type="submit"
                color="primary"
                icon="ti ti-chevrons-right"
                icon-position="right"
            >
                {{ trans('packages/get-started::get-started.finish') }}
            </x-core::button>
        </div>
    </div>
</x-core::modal>

<x-core::modal
    class="close-get-started-modal"
    size="lg"
    :close-button="false"
    data-bs-backdrop="static"
>
    <x-core::modal.close-button />

    <div class="text-center">
        <h2 class="mt-5">{{ trans('packages/get-started::get-started.exit_wizard_title') }}</h2>
    </div>

    <x-slot:footer>
        <div class="w-100">
            <div class="row gap-2 gap-md-0">
                <div class="col-12 col-md-6">
                    <x-core::button
                        type="button"
                        color="primary"
                        class="w-100 js-close-wizard"
                    >
                        {{ trans('packages/get-started::get-started.exit_wizard_confirm') }}
                    </x-core::button>
                </div>
                <div class="col-12 col-md-6">
                    <x-core::button
                        type="button"
                        class="w-100 text-primary js-back-to-wizard"
                    >
                        {{ trans('packages/get-started::get-started.exit_wizard_cancel') }}
                    </x-core::button>
                </div>
            </div>
        </div>
    </x-slot:footer>
</x-core::modal>
