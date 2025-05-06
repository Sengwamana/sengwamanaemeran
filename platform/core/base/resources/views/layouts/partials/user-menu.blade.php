<x-core::dropdown
    wrapper-class="nav-item"
    :has-arrow="true"
    position="end"
>
    <x-slot:trigger>
        <a
            href="{{ Auth::guard()->user()->url }}"
            class="p-0 nav-link d-flex lh-1 text-reset"
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
