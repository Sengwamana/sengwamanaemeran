@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    @php
        do_action(BASE_ACTION_TOP_FORM_CONTENT_NOTIFICATION, request(), THEME_OPTIONS_MODULE_SCREEN_NAME);
    @endphp

    <x-core::card class="theme-option">
        <x-core::form
            :url="route('theme.options')"
            method="post"
        >
            <x-core::card.header>
                <x-core::card.title>
                    {{ trans('packages/theme::theme.theme_options') }}
                </x-core::card.title>

                <x-core::card.actions>
                    <div class="btn-list justify-content-end align-items-center">
                        {!! apply_filters(THEME_OPTIONS_ACTION_META_BOXES, null, THEME_OPTIONS_MODULE_SCREEN_NAME) !!}
                        <x-core::button
                            type="submit"
                            color="primary"
                        >
                            {{ trans('packages/theme::theme.save_changes') }}
                        </x-core::button>
                    </div>
                </x-core::card.actions>
            </x-core::card.header>

            <x-core::card.body class="p-0">
                <div class="d-block d-md-flex">
                    <div
                        class="nav mb-md-0 flex-column nav-pills overflow-hidden p-3"
                        id="themeOptionTab"
                        role="tablist"
                        aria-orientation="vertical"
                    >
                        @foreach ($sections as $section)
                            <a
                                href="{{ route('theme.options', $section['id']) }}{{ request()->query('ref_lang') ? '?ref_lang=' . request()->query('ref_lang') : ''}}"
                                @class(['nav-link w-100', 'active' => $currentSection['id'] === $section['id']])
                                id="{{ $section['id'] }}-tab"
                                role="tab"
                                aria-controls="{{ $section['id'] }}"
                                aria-selected="{{ $currentSection['id'] === $section['id'] }}"
                                title="{{ $section['title'] }}"
                            >
                                @if ($section['icon'])
                                    <x-core::icon
                                        :name="$section['icon']"
                                        class="me-2"
                                    />
                                @endif
                                <span class="text-truncate">{{ $section['title'] }}</span>
                            </a>
                        @endforeach
                    </div>
                    <div
                        class="tab-content w-100 p-3"
                        id="themeOptionTabContent"
                    >
                        <div
                            class="tab-pane fade show active"
                            id="{{ $currentSection['id'] }}"
                            role="tabpanel"
                            aria-labelledby="{{ $currentSection['id'] }}-tab"
                            tabindex="0"
                        >
                            @isset($currentSection['description'])
                                <p class="mb-3">{!! BaseHelper::clean($currentSection['description']) !!}</p>
                            @endisset

                            @foreach (ThemeOption::constructFields($currentSection['id']) as $field)
                                @if (Arr::get($field, 'type') === 'hidden')
                                    {!! ThemeOption::renderField($field) !!}
                                @else
                                    <x-core::form-group
                                        class="{{ $errors->has($field['attributes']['name'] ?? $field['id']) ? 'has-error' : null }}"
                                    >
                                        <x-core::form.label
                                            :for="$field['id']"
                                            :label="$field['label']"
                                        />
                                        {!! ThemeOption::renderField($field) !!}
                                        @if (array_key_exists('helper', $field))
                                            <x-core::form.helper-text>
                                                {!! BaseHelper::clean($field['helper']) !!}
                                            </x-core::form.helper-text>
                                        @endif
                                        @endif
                                    </x-core::form-group>
                                    @endforeach
                        </div>
                    </div>
                </div>
            </x-core::card.body>

            <x-core::card.footer>
                <div class="btn-list justify-content-end align-items-center">
                    {!! apply_filters(THEME_OPTIONS_ACTION_META_BOXES, null, THEME_OPTIONS_MODULE_SCREEN_NAME) !!}
                    <x-core::button
                        type="submit"
                        color="primary"
                    >
                        {{ trans('packages/theme::theme.save_changes') }}
                    </x-core::button>
                </div>
            </x-core::card.footer>
        </x-core::form>
    </x-core::card>
@endsection
