<div class="image-box attachment-wrapper">
    <input
        class="attachment-url"
        name="{{ $name }}"
        type="hidden"
        value="{{ $value }}"
    >
    @if (!is_in_admin(true) || !auth()->check())
        <input
            class="media-file-input"
            type="file"
            style="display: none;"
            @if ($name) name="{{ $name }}_input" @endif
        >
    @endif
    <div class="position-relative">
        <div @class(['d-flex align-items-center gap-1 attachment-details form-control mb-2 pe-5', 'hidden' => ! $value])>
            <x-core::icon name="ti ti-file" class="me-1" style="--bb-icon-size: 1.5rem" />
            <div class="attachment-info text-truncate">
                <a href="{{ (($url ?? $value) ? RvMedia::url($url ?? $value) : null) }}" target="_blank" data-bs-toggle="tooltip" title="{{ $value }}">
                    {{ $value }}
                </a>
                <small class="d-block">{{ RvMedia::getFileSize($value) }}</small>
            </div>
        </div>

        <a
            href="javascript:void(0);"
            class="text-body text-decoration-none position-absolute end-0 me-2"
            data-bb-toggle="media-file-remove"
            @style(['top: 0.5rem', 'display: none' => ! $value])
            data-bs-toggle="tooltip"
            data-bs-placement="top"
            title="{{ trans('core/base::forms.remove_file') }}"
        >
            <x-core::icon name="ti ti-x" style="--bb-icon-size: 1rem" />
        </a>
    </div>
    <div class="image-box-actions">
        <a
            href="javascript:void(0);"
            @class(['btn_gallery' => is_in_admin(true) && auth()->check(), 'media-select-file' => !is_in_admin(true) || !auth()->check()])
            data-result="{{ $name }}"
            data-action="{{ $attributes['action'] ?? 'attachment' }}"
            size="sm"
            icon="ti ti-paperclip"
        >
            {{ trans('core/base::forms.choose_file') }}
        </a>
    </div>
</div>
