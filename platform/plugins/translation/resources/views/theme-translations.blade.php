@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <x-core::alert type="warning">
        {{ trans('plugins/translation::translation.theme_translations_instruction') }}

        <p class="mt-3 mb-0">
            {!! trans(
                'plugins/translation::translation.re_import_alert',
                ['here' => Html::link('#', trans('plugins/translation::translation.here'), ['data-bs-toggle' => 'modal', 'data-bs-target' => '#confirm-re-import-modal'])]
            ) !!}
        </p>
    </x-core::alert>

    <div class="theme-translation">
        <div class="row">
            <div class="col-md-6">
                <p>{{ trans('plugins/translation::translation.translate_from') }}
                    <strong class="text-info">{{ $defaultLanguage ? $defaultLanguage['name'] : 'en' }}</strong>
                    {{ trans('plugins/translation::translation.to') }}
                    <strong class="text-info">{{ $group['name'] }}</strong>
                </p>
            </div>
            <div class="col-md-6">
                <div class="text-end">
                    @include(
                        'plugins/translation::partials.list-theme-languages-to-translate',
                        ['groups' => $groups, 'group' => $group, 'route' => 'translations.theme-translations']
                    )
                </div>
            </div>
        </div>

        @if (count($groups) < 1)
            <p class="text-warning">{{ trans('plugins/translation::translation.no_other_languages') }}</p>
        @endif

        @if (count($groups) > 0 && $group)
            {!! apply_filters('translation_theme_translation_header', null, $groups, $group) !!}

            {!! $translationTable->renderTable() !!}
        @endif
    </div>
@endsection

@push('footer')
    <x-core::modal.action
        id="confirm-re-import-modal"
        :title="trans('plugins/translation::translation.import_translations')"
        :description="trans('plugins/translation::translation.import_translations_description')"
        type="warning"
        :submit-button-attrs="['class' => 'button-re-import', 'data-url' => route('translations.theme-translations.re-import')]"
        :submit-button-label="trans('core/base::base.yes')"
    />
@endpush
