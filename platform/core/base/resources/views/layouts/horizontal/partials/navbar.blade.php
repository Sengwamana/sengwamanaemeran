<header
    class="navbar navbar-expand-md d-print-none"
    data-bs-theme="dark"
>
    <div class="{{ AdminAppearance::getContainerWidth() }}">
        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbar-menu"
            aria-controls="navbar-menu"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>
        <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
            @include('core/base::partials.logo')
        </h1>

        <div class="flex-row navbar-nav order-md-last">
            <div class="nav-item d-none d-md-flex me-3">
                @include('core/base::global-search.navbar-input')
            </div>

            @if (BaseHelper::getAdminPrefix() != '')
                <div class="nav-item d-none d-md-flex me-3">
                    <div class="btn-list">
                        <x-core::button
                            tag="a"
                            :href="url('/')"
                            icon="ti ti-world"
                            target="_blank"
                        >
                            {{ trans('core/base::layouts.view_website') }}
                        </x-core::button>
                    </div>
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
    </div>
</header>

@include('core/base::layouts.horizontal.partials.header-expand')
