@once
    <div class="nav-item d-none d-md-flex me-2">
        <a
            class="px-0 nav-link"
            data-bs-toggle="offcanvas"
            href="#notification-sidebar"
            role="button"
            aria-controls="notification-sidebar"
        >
            <x-core::icon name="ti ti-bell" />
            <span
                class="badge bg-blue text-blue-fg badge-pill notification-count">{{ number_format($countNotificationUnread) }}</span>
        </a>
    </div>
@endonce
