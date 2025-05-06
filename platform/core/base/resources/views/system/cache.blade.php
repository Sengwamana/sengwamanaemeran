@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    <x-core::card>
        <x-core::card.header>
            <x-core::card.title>
                <x-core::icon name="ti ti-refresh" />
                {{ trans('core/base::cache.cache_commands') }}
            </x-core::card.title>
        </x-core::card.header>

        <div class="list-group list-group-flush">
            <div class="list-group-item">
                <div class="row align-items-center gap-3 justify-content-between">
                    <div class="col-auto">
                        {{ trans('core/base::cache.commands.clear_cms_cache.description') }}
                    </div>
                    <div class="col-auto">
                        <x-core::button
                            type="button"
                            color="danger"
                            class="btn-clear-cache"
                            data-type="clear_cms_cache"
                            data-url="{{ route('system.cache.clear') }}"
                        >
                            {{ trans('core/base::cache.commands.clear_cms_cache.title') }}
                        </x-core::button>
                    </div>
                </div>
            </div>

            <div class="list-group-item">
                <div class="row align-items-center gap-3 justify-content-between">
                    <div class="col-auto">
                        {{ trans('core/base::cache.commands.refresh_compiled_views.description') }}
                    </div>
                    <div class="col-auto">
                        <x-core::button
                            type="button"
                            color="warning"
                            class="btn-clear-cache"
                            data-type="refresh_compiled_views"
                            data-url="{{ route('system.cache.clear') }}"
                        >
                            {{ trans('core/base::cache.commands.refresh_compiled_views.title') }}
                        </x-core::button>
                    </div>
                </div>
            </div>

            <div class="list-group-item">
                <div class="row align-items-center gap-3 justify-content-between">
                    <div class="col-auto">
                        {{ trans('core/base::cache.commands.clear_config_cache.description') }}
                    </div>
                    <div class="col-auto">
                        <x-core::button
                            type="button"
                            class="btn-clear-cache"
                            data-type="clear_config_cache"
                            data-url="{{ route('system.cache.clear') }}"
                        >
                            {{ trans('core/base::cache.commands.clear_config_cache.title') }}
                        </x-core::button>
                    </div>
                </div>
            </div>

            <div class="list-group-item">
                <div class="row align-items-center gap-3 justify-content-between">
                    <div class="col-auto">
                        {{ trans('core/base::cache.commands.clear_route_cache.description') }}
                    </div>
                    <div class="col-auto">
                        <x-core::button
                            type="button"
                            class="btn-clear-cache"
                            data-type="clear_route_cache"
                            data-url="{{ route('system.cache.clear') }}"
                        >
                            {{ trans('core/base::cache.commands.clear_route_cache.title') }}
                        </x-core::button>
                    </div>
                </div>
            </div>

            <div class="list-group-item">
                <div class="row align-items-center gap-3 justify-content-between">
                    <div class="col-auto">
                        {{ trans('core/base::cache.commands.clear_log.description') }}
                    </div>
                    <div class="col-auto">
                        <x-core::button
                            type="button"
                            class="btn-clear-cache"
                            data-type="clear_log"
                            data-url="{{ route('system.cache.clear') }}"
                        >
                            {{ trans('core/base::cache.commands.clear_log.title') }}
                        </x-core::button>
                    </div>
                </div>
            </div>
        </div>
    </x-core::card>
@endsection
