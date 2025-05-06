@props(['id', 'icon' => null, 'label' => null, 'isActive' => false])

<li class="nav-item">
    <a
        href="#{{ $id }}"
        @class(['nav-link', 'active' => $isActive])
        data-bs-toggle="tab"
    >
        @if ($icon)
            <x-core::icon
                name="{{ $icon }}"
                class="me-2"
            />
        @endif

        @if ($label)
            {{ $label }}
        @endif
    </a>
</li>
