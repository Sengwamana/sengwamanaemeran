@php
    $mathCaptcha = app('math-captcha');

    $options = [
        ...$options,
        'label_attr' => [
            'class' => 'form-label required',
        ],
        'label' => $mathCaptcha->label(),
    ];
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
    <x-slot:label>
        @if ($showLabel && $options['label'] !== false && $options['label_show'])
            {!! Form::customLabel($name, $options['label'], $options['label_attr']) !!}
        @endif
    </x-slot:label>

    {!! $mathCaptcha->input([
        'class' => 'form-control',
        'id' => 'math-group',
        'placeholder' => $showLabel ? $mathCaptcha->getMathLabelOnly() . ' = ?' : $mathCaptcha->label(),
    ]) !!}
</x-core::form.field>
