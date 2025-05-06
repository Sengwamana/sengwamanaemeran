<div class="d-flex align-items-start gap-2 mb-1">
    @if ($item->website)
        <a href="{{ $item->website }}" target="_blank">
            <span class="avatar" style="background-image: url({{ $item->avatar_url }})"></span>
        </a>
    @else
        <span class="avatar" style="background-image: url({{ $item->avatar_url }})"></span>
    @endif
    <div>
        <span class="d-block fw-medium">{{ $item->name }}</span>
        <a href="mailto:{{ $item->email }}" class="small">{{ $item->email }}</a>
    </div>
</div>

@if ($item->website)
    <a href="{{ $item->website }}" target="_blank" class="small">
        {{ $item->website }}

        <x-core::icon name="ti ti-external-link" size="sm" />
    </a>
@endif
