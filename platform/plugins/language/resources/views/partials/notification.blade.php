<x-core::alert
    type="info"
    icon="ti ti-info-circle"
>
    {!! BaseHelper::clean(
        trans('plugins/language::language.current_language_edit_notification', ['language' => $language]),
    ) !!}
</x-core::alert>
