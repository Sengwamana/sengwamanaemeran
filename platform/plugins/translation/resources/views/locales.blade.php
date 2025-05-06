@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    <div class="row">
        <div class="col-md-5">
            <x-core::card class="mb-3">
                <x-core::card.body>
                    <x-core::form
                        class="add-locale-form"
                        :url="route('translations.locales')"
                        method="post"
                    >
                        <x-core::form.select
                            :label="trans('plugins/translation::translation.locale')"
                            name="locale"
                            :options="['' => trans('plugins/translation::translation.select_locale')] +
                                collect($locales)
                                    ->map(fn($item, $key) => $item . ' - ' . $key)
                                    ->all()"
                            :searchable="true"
                        />
                        <x-core::button
                            type="submit"
                            color="primary"
                        >
                            {{ trans('plugins/translation::translation.add_new_locale') }}
                        </x-core::button>
                    </x-core::form>
                </x-core::card.body>
            </x-core::card>
        </div>
        <div class="col-md-7">
            <x-core::card>
                <x-core::card.header>
                    <x-core::card.title>{{ trans('plugins/translation::translation.locales') }}</x-core::card.title>
                </x-core::card.header>
                <div class="table-responsive">
                    <table class="table card-table table-hover table-language">
                        <thead>
                            <tr>
                                <th class="text-start">{{ trans('plugins/translation::translation.name') }}</th>
                                <th>{{ trans('plugins/translation::translation.locale') }}</th>
                                <th>{{ trans('plugins/translation::translation.is_default') }}</th>
                                <th class="text-end">{{ trans('plugins/translation::translation.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($existingLocales as $item)
                                @include('plugins/translation::partials.locale-item', compact('item'))
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-core::card>
        </div>
    </div>

    <x-core::modal.action
        type="danger"
        class="modal-confirm-delete"
        :title="trans('core/base::tables.confirm_delete')"
        :description="trans('plugins/translation::translation.confirm_delete_message', ['lang_path' => lang_path()])"
        :submit-button-label="trans('core/base::tables.delete')"
        :submit-button-attrs="['class' => 'delete-crud-entry']"
    />

    <x-core::modal.action
        class="modal-confirm-import-locale"
        :title="trans('plugins/translation::translation.import_available_locale_confirmation')"
        :description="BaseHelper::clean(
            trans('plugins/translation::translation.import_available_locale_confirmation_content', [
                'lang_path' => Html::tag('strong', lang_path())->toHtml(),
            ]),
        )"
        type="info"
        :submit-button-attrs="['class' => 'button-confirm-import-locale']"
        :submit-button-label="trans('plugins/translation::translation.download_locale')"
    />
@endsection
