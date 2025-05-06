@props(['label', 'name' => null, 'checked' => false, 'label' => null, 'id' => null, 'single' => false, 'wrapperClass' => null])

@php($id = $attributes->get('id', $name) ?? Str::random(8))

<label @class([
    'form-check form-switch d-inline-block',
    'form-check-single' => $single,
    $wrapperClass,
])>
    <input
        name="{{ $name }}"
        type="hidden"
        value="0"
    />
    <input
        {{ $attributes->merge(['class' => 'form-check-input', 'name' => $name, 'type' => 'checkbox', 'value' => '1', 'id' => $id]) }}
        @checked($checked)
    />

    @if ($label)
        <span class="form-check-label">{!! $label !!}</span>
    @endif
</label>
