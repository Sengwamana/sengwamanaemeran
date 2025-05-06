@php
    $options['attr']['label'] = $options['label'];
    $options['attr']['label_attr'] = Arr::get($options, 'label_attr');
    $isShowLabel = $showLabel && $options['label'] && $options['label_show'];

    if (! $isShowLabel) {
        unset($options['attr']['label']);
    }

    $options['label'] = false;
@endphp

<x-core::form.field
    :showLabel="$showLabel"
    :showField="$showField"
    :options="$options"
    :name="$name"
    :prepend="$prepend ?? null"
    :append="$append ?? null"
    :showError="$showError"
    :nameKey="$nameKey"
>
    {!! Form::onOffCheckbox($name, $options['value'], $options['attr']) !!}
</x-core::form.field>
