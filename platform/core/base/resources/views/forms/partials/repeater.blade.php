@php
    Assets::addScriptsDirectly('vendor/core/core/base/js/repeater-field.js');

    $values = array_values(is_array($value) ? $value : (array) json_decode($value ?: '[]', true));

    $added = [];

    if (! empty($values)) {
        for ($index = 0; $index < count($values); $index++) {
            $group = '';

            foreach ($fields as $key => $field) {
                $group .= view('core/base::forms.partials.repeater-item', compact('name', 'index', 'key', 'field', 'values'));
            }

            $added[] = view('core/base::forms.partials.repeater-group', compact('group'));
        }
    }

    $group = '';

    foreach ($fields as $key => $field) {
        $group .= view('core/base::forms.partials.repeater-item', [
            'name' => $name,
            'index' => '__key__',
            'key' => $key,
            'field' => $field,
            'values' => [],
        ]);
    }

    $defaultFields = [view('core/base::forms.partials.repeater-group', compact('group'))->render()];

    $repeaterId = 'repeater_field_' . md5($name) . uniqid('_');
@endphp

<input
    name="{{ $name }}"
    type="hidden"
    value="[]"
>

<div
    class="repeater-group"
    id="{{ $repeaterId }}_group"
    data-next-index="{{ count($added) }}"
>
    @foreach ($added as $field)
        <fieldset
            class="form-fieldset position-relative"
            data-id="{{ $repeaterId }}_{{ $loop->index }}"
            data-index="{{ $loop->index }}"
        >
            <legend class="d-flex justify-content-end align-items-center">
                <x-core::button
                    class="position-absolute remove-item-button"
                    data-target="repeater-remove"
                    data-id="{{ $repeaterId }}_{{ $loop->index }}"
                    icon="ti ti-x"
                    :icon-only="true"
                    size="sm"
                />
            </legend>

            <div>{!! $field !!}</div>
        </fieldset>
    @endforeach
</div>

<div class="mb-3">
    <x-core::button
        data-target="repeater-add"
        data-id="{{ $repeaterId }}"
        type="button"
    >
        {{ __('Add new') }}
    </x-core::button>
</div>

@if(request()->ajax())
    @include('core/base::forms.partials.repeater-template', compact('repeaterId', 'defaultFields'))
@else
    @push('footer')
       @include('core/base::forms.partials.repeater-template', compact('repeaterId', 'defaultFields'))
    @endpush
@endif
