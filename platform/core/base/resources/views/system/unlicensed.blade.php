<x-core::layouts.base body-class="d-flex flex-column" :body-attributes="['data-bs-theme' => 'dark']">
    <x-slot:title>
        @yield('title')
    </x-slot:title>

    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                @include('core/base::partials.logo')
            </div>

            <x-core::card size="md">
                <x-core::card.body>
                    <h2 class="mb-3 text-center">Requires License Activation</h2>

                    <p class="text-secondary mb-4">
                        If you want to continue to use our platform, please activate license first.
                    </p>

                    <ul class="list-unstyled space-y">
                        <li class="row g-2">
                            <span class="col-auto">
                                <x-core::icon
                                    name="ti ti-check"
                                    class="me-1 text-success"
                                />
                            </span>
                            <span class="col">
                                <strong class="d-block">Get Updates, FOREVER!</strong>
                                <span class="d-block text-secondary">Your website is always up-to-date.</span>
                            </span>
                        </li>

                        <li class="row g-2">
                            <span class="col-auto">
                                <x-core::icon
                                    name="ti ti-check"
                                    class="me-1 text-success"
                                />
                            </span>
                            <span class="col">
                                <strong class="d-block">Get Support From Our Dev Team</strong>
                                <span class="d-block text-secondary">You've a problem. Don't worry. We are here to help
                                    you everytime you need.</span>
                            </span>
                        </li>

                        <li class="row g-2">
                            <span class="col-auto">
                                <x-core::icon
                                    name="ti ti-check"
                                    class="me-1 text-success"
                                />
                            </span>
                            <span class="col">
                                <strong class="d-block">Get Free Plugins</strong>
                                <span class="d-block text-secondary">Unlimited plugins, extended your website from <a
                                        href="https://marketplace.botble.com"
                                        target="_blank"
                                    >our marketplace <x-core::icon name="ti ti-external-link" /></a>.</span>
                            </span>
                        </li>
                    </ul>

                    <div class="my-2">
                        <x-core::button
                            color="primary"
                            class="w-100"
                            data-bs-toggle="modal"
                            data-bs-target="#quick-activation-license-modal"
                            aria-label="close"
                        >
                            Activate License
                        </x-core::button>
                    </div>

                    <div>
                        <form
                            action="{{ route('unlicensed.skip') }}"
                            method="POST"
                        >
                            @csrf

                            @if($redirectUrl)
                                <input type="hidden" name="redirect_url" value="{{ $redirectUrl}}" / >
                            @endif

                            <x-core::button
                                type="submit"
                                class="w-100"
                                color="link"
                                size="sm"
                            >Skip</x-core::button>
                        </form>
                    </div>
                </x-core::card.body>
            </x-core::card>
        </div>
    </div>

    @include('core/base::system.partials.license-activation-modal')
</x-core::layouts.base>
