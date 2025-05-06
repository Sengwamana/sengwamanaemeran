@include('core/base::layouts.' . AdminAppearance::getCurrentLayout() . '.partials.aside')

<header
    class="navbar navbar-expand-md d-none d-lg-flex d-print-none"
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

        <div class="flex-row navbar-nav">
            <div class="d-flex align-items-center me-3">
                @include('core/base::global-search.navbar-input')
            </div>
        </div>

        <div class="flex-row navbar-nav order-md-last">
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
