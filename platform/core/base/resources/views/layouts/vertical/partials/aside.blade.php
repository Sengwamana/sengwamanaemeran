<aside
    @class([
        'navbar navbar-vertical navbar-expand-lg flex-auto',
        'navbar-minimal' => Auth::user()->getMeta('minimal_sidebar', 'no') === 'yes',
    ])
    data-bs-theme="dark"
    id="sidebar-menu-main"
>
    <div class="{{ AdminAppearance::getContainerWidth() }}">
        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#sidebar-menu"
            aria-controls="sidebar-menu"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >
            <x-core::icon name="ti ti-menu-2" />
        </button>

        <h2 class="d-block d-lg-none navbar-brand navbar-brand-autodark">
            @include('core/base::partials.logo')
        </h2>

        <div class="navbar-nav flex-row d-lg-none">
            <x-core::dropdown
                wrapper-class="nav-item"
                :has-arrow="true"
                position="end"
            >
                <x-slot:trigger>
                    <a
                        href="#"
                        class="nav-link d-flex lh-1 text-reset p-0"
                        data-bs-toggle="dropdown"
                        aria-label="{{ __('Open user menu') }}"
                    >
                        <span
                            class="crop-image-original avatar avatar-sm"
                            style="background-image: url({{ Auth::guard()->user()->avatar_url }})"
                        ></span>
                        <div class="d-none d-xl-block ps-2">
                            <div>{{ Auth::guard()->user()->name }}</div>
                            <div class="mt-1 small text-muted">{{ Auth::guard()->user()->email }}</div>
                        </div>
                    </a>
                </x-slot:trigger>

                <x-core::dropdown.item
                    :href="Auth::guard()->user()->url"
                    :label="trans('core/base::layouts.profile')"
                    icon="ti ti-user"
                />

                <x-core::dropdown.item
                    :href="route('access.logout')"
                    :label="trans('core/base::layouts.logout')"
                    icon="ti ti-logout"
                />
            </x-core::dropdown>
        </div>

        @include('core/base::layouts.vertical.partials.sidebar')
    </div>
</aside>
