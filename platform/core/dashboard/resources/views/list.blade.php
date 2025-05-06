@extends(BaseHelper::getAdminMasterLayoutTemplate())

@push('header-action')
    @if (count($widgets) > 0)
        <x-core::button
            color="primary"
            :outlined="true"
            class="manage-widget"
            data-bs-toggle="modal"
            data-bs-target="#widgets-management-modal"
            icon="ti ti-layout-dashboard"
        >
            {{ trans('core/dashboard::dashboard.manage_widgets') }}
        </x-core::button>
    @endif
@endpush

@section('content')
    <div class="row">
        <div class="col-12">
            @if (config('core.base.general.enable_system_updater') && Auth::user()->isSuperUser())
                <v-check-for-updates
                    check-update-url="{{ route('system.check-update') }}"
                    v-slot="{ hasNewVersion, message }"
                    v-cloak
                >
                    <x-core::alert
                        v-if="hasNewVersion"
                        type="warning"
                    >
                        @{{ message }}, please go to <a
                            href="{{ route('system.updater') }}"
                            class="text-warning fw-bold"
                        >System Updater</a> to upgrade to the latest version!
                    </x-core::alert>
                </v-check-for-updates>
            @endif
        </div>

        <div class="col-12">
            {!! apply_filters(DASHBOARD_FILTER_ADMIN_NOTIFICATIONS, null) !!}
        </div>

        <div class="col-12">
            <div class="row row-cards">
                @foreach ($statWidgets as $widget)
                    {!! $widget !!}
                @endforeach
            </div>
        </div>
    </div>

    <div class="mb-3 col-12">
        {!! apply_filters(DASHBOARD_FILTER_TOP_BLOCKS, null) !!}
    </div>

    <div class="col-12">
        <div
            id="list_widgets"
            class="row row-cards"
            data-bb-toggle="widgets-list"
            data-url="{{ route('dashboard.update_widget_order') }}"
        >
            @foreach ($userWidgets as $widget)
                {!! $widget !!}
            @endforeach
        </div>
    </div>
@endsection

@push('footer')
    @include('core/dashboard::partials.modals', compact('widgets'))
@endpush
