@php
    $attributes['class'] = Arr::get($attributes, 'class', '') . str_replace(Arr::get($attributes, 'class'), '', ' form-control');
    $attributes['placeholder'] = BaseHelper::getDateFormat();
    $attributes['data-input'] = '';
    $attributes['readonly'] = $attributes['readonly'] ?? 'readonly';

    if (App::getLocale() != 'en') {
        Assets::addScriptsDirectly('https://npmcdn.com/flatpickr@4.6.13/dist/l10n/index.js');
    }

    if (! empty($attributes['data-options'])) {
        $attributes['data-options'] = json_encode($attributes['data-options']);
    }
@endphp

<div class="input-group datepicker">
    {!! Form::text($name, $value, $attributes) !!}
    <x-core::button data-toggle icon="ti ti-calendar" :icon-only="true" />
    <x-core::button data-clear icon="ti ti-x" :icon-only="true" class="text-danger" />
</div>
