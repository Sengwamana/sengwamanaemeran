@php
    if (BaseHelper::hasIcon($value)) {
        $icon = BaseHelper::renderIcon($value);
    } else {
        $icon = '<i class="' . $value . '"></i>';
    }
@endphp

<select name="{{ $name }}" data-bb-core-icon data-url="{{ route('core-icons') }}" data-placeholder="{{ $attributes['placeholder'] ?? ($attributes['data-placeholder'] ?? trans('core/base::forms.select_placeholder')) }}" {!! Html::attributes($attributes) !!}>
    @if ($value)
        <option value="{{ $value }}" selected>{{ $icon }}</option>
    @endif
</select>
