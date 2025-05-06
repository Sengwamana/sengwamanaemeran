@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <x-core::card>
        <x-core::card.header>
            <ul class="nav nav-tabs card-header-tabs">
                <li class="nav-item">
                    <a
                        href="#tabs-detail"
                        class="nav-link active"
                        data-bs-toggle="tab"
                    >{{ trans('core/base::tabs.detail') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a
                        href="#tabs-settings"
                        class="nav-link"
                        data-bs-toggle="tab"
                    >{{ trans('plugins/language::language.settings') }}
                    </a>
                </li>
                {!! apply_filters(BASE_FILTER_REGISTER_CONTENT_TABS, null, new Botble\Language\Models\Language()) !!}
            </ul>
        </x-core::card.header>
        <x-core::card.body>
            <div class="tab-content">
                <div
                    class="tab-pane active"
                    id="tabs-detail"
                >
                    <div class="row">
                        <div class="col-md-5">
                            @php
                                do_action(BASE_ACTION_META_BOXES, 'top', new Botble\Language\Models\Language());
                            @endphp

                            <input
                                type="hidden"
                                id="lang_id"
                                value="0"
                            >
                            <input
                                type="hidden"
                                id="language_flag_path"
                                value="{{ BASE_LANGUAGE_FLAG_PATH }}"
                            >

                            <x-core::form.select
                                name="language_id"
                                :label="trans('plugins/language::language.choose_language')"
                                :helper-text="trans('plugins/language::language.choose_language_helper')"
                                searchable
                            >
                                <option>{{ trans('plugins/language::language.select_language') }}
                                </option>
                                @foreach ($languages as $key => $language)
                                    <option
                                        value="{{ $key }}"
                                        data-language="{{ Js::encode($language) }}"
                                    > {{ $language[2] }} - {{ $language[1] }}
                                    </option>
                                @endforeach
                            </x-core::form.select>

                            <x-core::form.text-input
                                :label="trans('plugins/language::language.language_name')"
                                name="lang_name"
                                :helper-text="trans('plugins/language::language.language_name_helper')"
                            />

                            <x-core::form.select
                                name="lang_locale"
                                :label="trans('plugins/language::language.locale')"
                                :helper-text="trans('plugins/language::language.locale_helper')"
                                searchable
                            >
                                <option>{{ trans('plugins/language::language.select_locale') }}</option>
                                @foreach ($localeKeys as $localeKey)
                                    <option value="{{ $localeKey }}">{{ $localeKey }}</option>
                                @endforeach
                            </x-core::form.select>

                            <x-core::form.select
                                name="lang_code"
                                :label="trans('plugins/language::language.language_code')"
                                :helper-text="trans('plugins/language::language.language_code_helper')"
                                searchable
                            >
                                <option>{{ trans('plugins/language::language.select_language_code') }}
                                </option>
                                @foreach ($languageCodes as $key => $code)
                                    <option value="{{ $code }}">{{ $code }}</option>
                                @endforeach
                            </x-core::form.select>

                            <x-core::form.radio-list
                                name="lang_rtl"
                                :label="trans('plugins/language::language.text_direction')"
                                value="0"
                                :options="[
                                    '0' => trans('plugins/language::language.left_to_right'),
                                    '1' => trans('plugins/language::language.right_to_left'),
                                ]"
                                :helper-text="trans('plugins/language::language.text_direction_helper')"
                            />

                            <x-core::form.select
                                :label="trans('plugins/language::language.flag')"
                                name="flag_list"
                                :options="['' => trans('plugins/language::language.select_flag')] + $flags"
                                :helper-text="trans('plugins/language::language.flag_helper')"
                                class="select-search-language"
                            />

                            <x-core::form.text-input
                                :label="trans('plugins/language::language.order')"
                                type="number"
                                name="lang_order"
                                :helper-text="trans('plugins/language::language.order_helper')"
                                value="0"
                            />

                            <x-core::button
                                type="submit"
                                color="primary"
                                id="btn-language-submit"
                                data-store-url="{{ route('languages.store') }}"
                                data-update-url="{{ route('languages.edit') }}"
                                data-add-language-text="{{ trans('plugins/language::language.add_new_language') }}"
                                data-update-language-text="{{ trans('plugins/language::language.update') }}"
                            >
                                {{ trans('plugins/language::language.add_new_language') }}
                            </x-core::button>

                            @php
                                do_action(BASE_ACTION_META_BOXES, 'advanced', new Botble\Language\Models\Language());
                            @endphp
                        </div>
                        <div class="col-md-7">
                            <div class="table-responsive">
                                <x-core::table class="table-language">
                                    <x-core::table.header>
                                        <x-core::table.header.cell>
                                            {{ trans('plugins/language::language.language_name') }}
                                        </x-core::table.header.cell>
                                        <x-core::table.header.cell>
                                            {{ trans('plugins/language::language.locale') }}
                                        </x-core::table.header.cell>
                                        <x-core::table.header.cell>
                                            {{ trans('plugins/language::language.code') }}
                                        </x-core::table.header.cell>
                                        <x-core::table.header.cell>
                                            {{ trans('plugins/language::language.default_language') }}
                                        </x-core::table.header.cell>
                                        <x-core::table.header.cell>
                                            {{ trans('plugins/language::language.order') }}
                                        </x-core::table.header.cell>
                                        <x-core::table.header.cell>
                                            {{ trans('plugins/language::language.actions') }}
                                        </x-core::table.header.cell>
                                    </x-core::table.header>
                                    <x-core::table.body>
                                        @if(count($activeLanguages))
                                            @each('plugins/language::partials.language-item', $activeLanguages, 'item')
                                        @else
                                            <tr>
                                                <td colspan="6" class="bg-gray-200">
                                                    {{ trans('plugins/language::language.no_languages') }}
                                                </td>
                                            </tr>
                                        @endif
                                    </x-core::table.body>
                                </x-core::table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane" id="tabs-settings">
                    {!! $languageSettingForm->renderForm() !!}
                </div>
            </div>
        </x-core::card.body>
    </x-core::card>

    <x-core::modal.action
        type="danger"
        class="modal-confirm-delete"
        :title="trans('core/base::tables.confirm_delete')"
        :description="trans('plugins/language::language.delete_confirmation_message')"
        :submit-button-label="trans('core/base::tables.delete')"
        :submit-button-attrs="['class' => 'delete-crud-entry']"
    />
@endsection
