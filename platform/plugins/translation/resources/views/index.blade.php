@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <x-core::alert type="warning">
        <p class="mb-0">
            {{ trans('plugins/translation::translation.theme_translations_instruction') }}
        </p>
    </x-core::alert>

    <div class="row">
        <div class="col-md-6">
            @if ($locale['locale'] !== 'en')
                <p>{{ trans('plugins/translation::translation.translate_from') }}
                    <strong class="text-info">{{ $defaultLanguage ? $defaultLanguage['name'] : 'en' }}</strong>
                    {{ trans('plugins/translation::translation.to') }}
                    <strong class="text-info">{{ $locale['name'] }}</strong>
                </p>
            @endif
        </div>
        <div class="col-md-6">
            <div class="text-end">
                @include(
                    'plugins/translation::partials.list-theme-languages-to-translate',
                    ['groups' => $locales, 'group' => $locale, 'route' => 'translations.index']
                )
            </div>
        </div>
    </div>

    <div class="translations-table">
        {{ $translationTable->renderTable() }}
    </div>
@endsection
