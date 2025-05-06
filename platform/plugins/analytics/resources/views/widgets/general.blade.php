<div class="row mb-2">
    <div class="col-lg-7">
        <div
            class="chart"
            id="stats-chart"
        ></div>
    </div>
    <div class="col-lg-5">
        <div
            id="world-map"
            style="height: 20rem;"
        ></div>
    </div>
</div>

<div class="col-12 mb-5">
    <div class="row row-cards px-2">
        <div class="col-sm-6 col-lg-3">
            <x-core::card class="analytic-card">
                <x-core::card.body class="p-3">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <x-core::icon
                                class="text-white bg-pink rounded p-1"
                                name="ti ti-eye"
                                size="md"
                            />
                        </div>
                        <div class="col mt-0">
                            <p class="text-secondary mb-0 fs-4">
                                {{ trans('plugins/analytics::analytics.sessions') }}
                            </p>
                            <h3 class="mb-n1 fs-1">
                                {{ number_format($sessions) }}
                            </h3>
                        </div>
                    </div>
                </x-core::card.body>
            </x-core::card>
        </div>

        <div class="col-sm-6 col-lg-3">
            <x-core::card class="analytic-card">
                <x-core::card.body class="p-3">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <x-core::icon
                                class="text-white bg-lime rounded p-1"
                                name="ti ti-users"
                                size="md"
                            />
                        </div>
                        <div class="col mt-0">
                            <p class="text-secondary mb-0 fs-4">
                                {{ trans('plugins/analytics::analytics.visitors') }}
                            </p>
                            <h3 class="mb-n1 fs-1">
                                {{ number_format($totalUsers) }}
                            </h3>
                        </div>
                    </div>
                </x-core::card.body>
            </x-core::card>
        </div>

        <div class="col-sm-6 col-lg-3">
            <x-core::card class="analytic-card">
                <x-core::card.body class="p-3">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <x-core::icon
                                class="text-white bg-azure rounded p-1"
                                name="ti ti-traffic-cone"
                                size="md"
                            />
                        </div>
                        <div class="col mt-0">
                            <p class="text-secondary mb-0 fs-4">
                                {{ trans('plugins/analytics::analytics.pageviews') }}
                            </p>
                            <h3 class="mb-n1 fs-1">
                                {{ number_format($screenPageViews) }}
                            </h3>
                        </div>
                    </div>
                </x-core::card.body>
            </x-core::card>
        </div>

        <div class="col-sm-6 col-lg-3">
            <x-core::card class="analytic-card">
                <x-core::card.body class="p-3">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <x-core::icon
                                class="text-white bg-yellow rounded p-1"
                                name="ti ti-bolt"
                                size="md"
                            />
                        </div>
                        <div class="col mt-0">
                            <p class="text-secondary mb-0 fs-4">
                                {{ trans('plugins/analytics::analytics.bounce_rate') }}
                            </p>
                            <h3 class="mb-n1 fs-1">
                                {{ round($bounceRate, 2) * 100 }}%
                            </h3>
                        </div>
                    </div>
                </x-core::card.body>
            </x-core::card>
        </div>
    </div>
</div>

<script>
    window.analyticsStats = {{ Js::from([
        'stats' => $chartStats,
        'countryStats' => $countryStats,
        'translations' => [
            'pageViews' => trans('plugins/analytics::analytics.pageviews'),
            'visits' => trans('plugins/analytics::analytics.visitors'),
        ],
    ]) }}
</script>
