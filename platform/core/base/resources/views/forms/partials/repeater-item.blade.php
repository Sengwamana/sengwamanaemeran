@php
    if ($field['type'] === 'select') {
        $field['type'] = 'customSelect';
    }

    $hiddenField = Form::hidden($name . '[' . $index . '][' . $key . '][key]', $field['attributes']['name']);
    $field['attributes']['name'] = $name . '[' . $index . '][' . $key . '][value]';
    $field['attributes']['value'] = Arr::get($values, $index . '.' . $key . '.value');
    $field['attributes']['options']['id'] = $id = 'repeater_field_' . md5($field['attributes']['name']) . uniqid('_');
    $field['attributes']['id'] = $id;
    $field['attributes']['label_attr']['for'] = $id;
@endphp

<x-core::form-group>
    <x-core::form.label :attributes="new Illuminate\View\ComponentAttributeBag(Arr::get($field, 'label_attr', []))">
        {{ $field['label'] }}
    </x-core::form.label>

    {{ $hiddenField }}

    {!! call_user_func_array([Form::class, $field['type']], array_values($field['attributes'])) !!}

    @if(! empty($field['helper']))
        <x-core::form.helper-text>
            {!! BaseHelper::clean($field['helper']) !!}
        </x-core::form.helper-text>
    @endif
</x-core::form-group>
