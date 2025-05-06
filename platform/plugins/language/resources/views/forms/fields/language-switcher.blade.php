@php
    $languages = Language::getActiveLanguage(['lang_id', 'lang_name', 'lang_code', 'lang_flag']);
@endphp

@if($languages->count() > 2)
    </div>
    <x-core::card.footer>
        <div>
            @include('plugins/language::partials.admin-list-language-chooser', [
                'params' => request()->route()->parameters(),
                'languages' => $languages,
            ])
        </div>
    </x-core::card.footer>
    <div>
@endif
