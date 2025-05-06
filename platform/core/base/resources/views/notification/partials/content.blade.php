@php($notifications = $notifications ?? collect())

@if ($notifications->isNotEmpty())
    <div class="offcanvas-header">
        <div>
            <h2
                class="offcanvas-title"
                id="notification-sidebar-label"
            >
                <span class="position-relative">
                    {{ trans('core/base::notifications.notifications') }}
                    <span
                        class="badge bg-blue text-blue-fg badge-notification badge-pill notification-count">{{ number_format($notificationsCount) }}</span>
                </span>
            </h2>
            <div class="d-flex gap-2 mt-2">
                <button
                    type="button"
                    class="fw-medium mark-all-notifications-as-read"
                    data-url="{{ route('notifications.read-all-notification') }}"
                >
                    {{ trans('core/base::notifications.mark_as_read') }}
                </button>
                <button
                    type="button"
                    class="fw-medium clear-notifications"
                    data-url="{{ route('notifications.destroy-all-notification') }}"
                >
                    {{ trans('core/base::notifications.clear') }}
                </button>
            </div>
        </div>
    </div>
@endif

<div
    @class(['offcanvas-body', 'p-0' => $notifications->isNotEmpty()])
    style="height: 92vh;"
>
    @include('core/base::notification.partials.notification-item', compact('notifications'))
</div>
