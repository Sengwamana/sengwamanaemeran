@if ($notifications->isNotEmpty())
    <div class="list-group list-group-flush">
        @foreach ($notifications as $notification)
            <li @class([
                'list-group-item list-group-item-action',
                'active' => $notification->read_at === null,
            ])>
                <div class="d-flex w-100 gap-2">
                    <div>
                        <x-core::icon
                            name="ti ti-bell"
                            size="md"
                            style="font-size: 1.5rem"
                        />
                    </div>
                    <div class="d-grid flex-grow-1 flex-shrink-1">
                        <h3 class="fs-4 mb-1 text-truncate">{!! BaseHelper::clean($notification->title) !!}</h3>
                        <time
                            class="text-muted"
                            datetime="{{ $notification->created_at->toIso8601String() }}"
                        >
                            {{ $notification->created_at->diffForHumans() }}
                        </time>
                        <p class="mt-2 mb-0">
                            {!! BaseHelper::clean($notification->description) !!}
                        </p>
                        @if (filled($notification->action_url))
                            <div class="mt-3">
                                <a
                                    class=""
                                    href="{{ route('notifications.read-notification', $notification) }}"
                                >
                                    {{ $notification->action_label ?: trans('core/base::notifications.view') }}
                                </a>
                            </div>
                        @endif
                    </div>
                    <button
                        type="button"
                        data-url="{{ route('notifications.destroy', $notification->id) }}"
                        class="btn-delete-notification"
                    >
                        <x-core::icon name="ti ti-x" />
                    </button>
                </div>
            </li>
        @endforeach

        @if ($notifications->previousPageUrl() || $notifications->nextPageUrl())
            <li class="list-group-item">
                <nav class="d-flex justify-content-between">
                    @if ($notifications->previousPageUrl())
                        <x-core::button
                            type="button"
                            data-url="{{ $notifications->previousPageUrl() }}"
                            class="btn-previous"
                        >{{ trans('core/base::notifications.previous') }}</x-core::button>
                    @else
                        <span></span>
                    @endif

                    @if ($notifications->nextPageUrl())
                        <x-core::button
                            type="button"
                            data-url="{{ $notifications->nextPageUrl() }}"
                            class="btn-next"
                        >{{ trans('core/base::notifications.next') }}</x-core::button>
                    @else
                        <span></span>
                    @endif
                </nav>
            </li>
        @endif
    </div>
@else
    <div class="text-center">
        <div class="mb-3">
            <x-core::icon
                name="ti ti-bell-off"
                size="md"
                class="text-secondary"
            />
        </div>

        <h3 class="mb-1">{{ trans('core/base::notifications.no_notification_here') }}</h3>
        <p class="text-muted">{{ trans('core/base::notifications.please_check_again_later') }}</p>
    </div>
@endif
