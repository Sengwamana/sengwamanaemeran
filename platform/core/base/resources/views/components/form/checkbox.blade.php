@props([
    'id' => null,
    'label' => null,
    'name' => null,
    'value' => null,
    'checked' => false,
    'helperText' => null,
    'inline' => false,
    'single' => false,
    'marginZero' => false
])

@php
    $labelClasses = Arr::toCssClasses([
        'form-check',
        'form-check-inline mb-3' => $inline,
        'form-check-single' => $single,
        'form-check m-0' => $marginZero,
    ]);

    if (isset($attributes['label_attr'])) {
        $labelAttr = $attributes['label_attr'];
        $labelAttr['class'] .= ' ' . $labelClasses;
        $labelAttr['class'] = trim(str_replace('form-label', '', $labelAttr['class']));
        unset($attributes['label_attr']);
    } else {
        $labelAttr = ['class' => $labelClasses];
    }
@endphp

<label {!! Html::attributes($labelAttr) !!}>
    <input
        {{ $attributes->merge(['type' => 'checkbox', 'id' => $id, 'name' => $name, 'class' => 'form-check-input']) }}
        value="{{ $value }}"
        @checked($name ? old($name, $checked) : $checked)
    >

    @if($label || $slot->isNotEmpty())
        <span class="form-check-label">
            {!! $label ? BaseHelper::clean($label) : $slot !!}
        </span>
    @endif

    @if ($helperText)
        <span class="form-check-description">{!! BaseHelper::clean($helperText) !!}</span>
    @endif
</label>
