<div
    class="help-ts"
    v-pre
>
    @if (!$icon)
        <x-core::icon name="info-circle" class="me-1" />
    @else
        {!! $icon !!}
    @endif
    <span>{!! $content !!}</span>
</div>
