<header
    class="navbar navbar-expand-md d-none d-lg-flex d-print-none"
    data-bs-theme="dark"
>
    <div class="container-fluid">
        <button
            class="navbar-toggler d-none d-lg-block me-2 ms-n1"
            type="button"
            data-bb-toggle="navbar-minimal"
            data-bb-target="#sidebar-menu-main"
            aria-controls="navbar-menu"
            aria-expanded="false"
            aria-label="Toggle navigation"
            data-url="{{ route('users.update-preferences', Auth::user()) }}"
            data-method="PATCH"
        >
            <x-core::icon name="ti ti-menu-2" />
        </button>

        <h1 class="navbar-brand navbar-brand-autodark me-4">
            @include('core/base::partials.logo')
        </h1>

        <div class="flex-row navbar-nav order-md-last">
            <div class="d-flex align-items-center me-3">
                @include('core/base::global-search.navbar-input')
            </div>

            @if (BaseHelper::getAdminPrefix() != '')
                <div class="d-flex align-items-center me-3">
                    <x-core::button
                        tag="a"
                        :href="url('/')"
                        icon="ti ti-world"
                        target="_blank"
                    >
                        {{ trans('core/base::layouts.view_website') }}
                    </x-core::button>
                </div>
            @endif

            <div class="d-none d-md-flex me-2">
                @include('core/base::layouts.partials.theme-toggle')

                @auth
                    {!! apply_filters(BASE_FILTER_TOP_HEADER_LAYOUT, null) !!}
                @endauth
            </div>

            @include('core/base::layouts.partials.user-menu')
        </div>

        <div
            class="collapse navbar-collapse"
            id="navbar-menu"
        ></div>
    </div>
</header>
