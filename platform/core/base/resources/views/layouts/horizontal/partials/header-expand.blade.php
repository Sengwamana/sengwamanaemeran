<header class="navbar-expand-md">
    <div
        class="collapse navbar-collapse"
        id="navbar-menu"
    >
        <div class="navbar">
            <div class="{{ AdminAppearance::getContainerWidth() }}">
                <div class="row flex-fill align-items-center">
                    <div class="col">
                        @include('core/base::layouts.horizontal.partials.navbar-nav', [
                            'autoClose' => 'outside',
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
