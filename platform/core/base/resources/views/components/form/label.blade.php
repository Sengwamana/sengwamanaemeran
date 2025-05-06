@props(['label', 'description' => null, 'helperText' => null])

<label {{ $attributes->merge(['class' => 'form-label']) }}>
    {{ $label ?? $slot }}

    @if ($description)
        <span class="form-label-description">
            {!! $description !!}
        </span>
    @endif

    @if ($helperText)
        <span
            class="form-help"
            data-bs-toggle="popover"
            data-bs-placement="top"
            data-bs-html="true"
            data-bs-content="{{ $helperText }}"
        >?</span>
    @endif
</label>
