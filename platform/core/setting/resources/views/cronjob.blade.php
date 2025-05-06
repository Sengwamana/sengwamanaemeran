@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <x-core-setting::section
        :title="trans('core/setting::setting.cronjob.name')"
        :description="trans('core/setting::setting.cronjob.description')"
    >
        <div class="input-group mb-3 cronjob">
            <input
                type="text"
                id="command"
                class="form-control"
                value="{{ $command }}"
                readonly
            >
            <button
                class="input-group-text px-3"
                id="copy-command"
                data-bb-toggle="clipboard"
                data-clipboard-target="#command"
                data-clipboard-message="{{ trans('core/setting::setting.cronjob.setup.copied') }}"
            >{{ trans('core/setting::setting.cronjob.copy_button') }}</button>
        </div>

        <div class="mt-2">
            @if (!$lastRunAt)
                <x-core::alert
                    type="info"
                    :title="trans('core/setting::setting.cronjob.is_not_ready')"
                />
            @elseif(Carbon\Carbon::now()->diffInMinutes($lastRunAt) <= 10)
                <x-core::alert
                    type="success"
                    :title="trans('core/setting::setting.cronjob.is_working')"
                >
                    <x-slot:subtitle>
                        {!! BaseHelper::clean(
                            trans('core/setting::setting.cronjob.last_checked', [
                                'time' => "<span class='fw-semibold'>{$lastRunAt->diffForHumans()}</span>",
                            ]),
                        ) !!}
                    </x-slot:subtitle>
                </x-core::alert>
            @else
                <x-core::alert
                    type="danger"
                    :title="trans('core/setting::setting.cronjob.is_not_working')"
                />
            @endif
        </div>

        <div class="mt-4 pt-4 border-top">
            <h3>{{ trans('core/setting::setting.cronjob.setup.name') }}</h3>
            <ol class="text-sm ps-3">
                <li>{{ trans('core/setting::setting.cronjob.setup.connect_to_server') }}
                </li>
                <li>{{ trans('core/setting::setting.cronjob.setup.open_crontab') }}</li>
                <li>{{ trans('core/setting::setting.cronjob.setup.add_cronjob') }}</li>
                <li>{{ trans('core/setting::setting.cronjob.setup.done') }}</li>
            </ol>

            <p class="border-top pt-4 text-sm mt-2">
                {!! BaseHelper::clean(trans('core/setting::setting.cronjob.setup.learn_more', [
                        'documentation' => sprintf(
                            '<a href="https://laravel.com/docs/10.x/scheduling" target="_blank" class="hover:underline text-primary-500">%s</a>',
                            trans('core/setting::setting.cronjob.setup.documentation')
                        )
                    ]))
                !!}
            </p>
        </div>
    </x-core-setting::section>
@stop
