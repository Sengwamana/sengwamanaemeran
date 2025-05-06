@if(AdminHelper::themeMode() === 'dark')
    <a
        href="{{ route('toggle-theme-mode', ['theme' => 'light']) }}"
        class="px-0 nav-link hide-theme-light"
        title="{{ __('Enable light mode') }}"
        data-bs-toggle="tooltip"
        data-bs-placement="bottom"
    >
        <x-core::icon name="ti ti-sun" />
    </a>
@else
    <a
        href="{{ route('toggle-theme-mode', ['theme' => 'dark']) }}"
        class="px-0 nav-link hide-theme-dark"
        title="{{ __('Enable dark mode') }}"
        data-bs-toggle="tooltip"
        data-bs-placement="bottom"
    >
        <x-core::icon name="ti ti-moon" />
    </a>
@endif
