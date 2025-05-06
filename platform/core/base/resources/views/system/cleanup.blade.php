@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div
                class="updater-box"
                dir="ltr"
            >
                <x-core::alert type="warning">
                    <ul class="mb-0 list-unstyled">
                        <li>{{ trans('core/base::system.cleanup.backup_alert') }}</li>
                        @if (!config('core.base.general.enabled_cleanup_database', false))
                            <li class="mt-2">{!! BaseHelper::clean(trans('core/base::system.cleanup.not_enabled_yet')) !!}</li>
                        @endif
                    </ul>
                </x-core::alert>
                <x-core::card>
                    <x-core::card.header>
                        <x-core::card.title>{{ trans('core/base::system.cleanup.messenger_choose_without_table') }}:</x-core::card.title>
                    </x-core::card.header>

                    <x-core::card.body class="p-0">
                        <x-core::form
                            :url="route('system.cleanup')"
                            method="post"
                            id="form-cleanup-database"
                        >
                            <div class="card-responsive">
                                <x-core::table>
                                    <x-core::table.header>
                                        <x-core::table.header.cell>
                                            #
                                        </x-core::table.header.cell>

                                        <x-core::table.header.cell>
                                            {{ trans('core/base::system.cleanup.table.name') }}
                                        </x-core::table.header.cell>

                                        <x-core::table.header.cell class="text-center">
                                            {{ trans('core/base::system.cleanup.table.count') }}
                                        </x-core::table.header.cell>
                                    </x-core::table.header>

                                    @foreach ($tables as $table)
                                        <x-core::table.body>
                                            <x-core::table.body.cell>
                                                <input
                                                    class="form-check-input"
                                                    @disabled(in_array($table, $disabledTables['disabled']))
                                                    @checked(in_array($table, $disabledTables['disabled']) || in_array($table, $disabledTables['checked']))
                                                    type="checkbox"
                                                    value="{{ $table }}"
                                                    name="tables[]"
                                                >
                                            </x-core::table.body.cell>

                                            <x-core::table.body.cell>
                                                {{ $table }}
                                            </x-core::table.body.cell>

                                            <x-core::table.body.cell class="text-center">
                                                {{ DB::table($table)->count() }}
                                            </x-core::table.body.cell>
                                        </x-core::table.body>
                                    @endforeach
                                </x-core::table>
                            </div>
                        </x-core::form>
                    </x-core::card.body>

                    <x-core::card.footer class="text-end">
                        <x-core::button
                            color="danger"
                            icon="ti ti-alert-triangle"
                            class="btn-trigger-cleanup"
                        >
                            {{ trans('core/base::system.cleanup.submit_button') }}
                        </x-core::button>
                    </x-core::card.footer>
                </x-core::card>
            </div>
        </div>
    </div>

    <x-core::modal.action
        id="cleanup-modal"
        :title="trans('core/base::system.cleanup.title')"
        :description="trans('core/base::system.cleanup.messenger_confirm_cleanup')"
        type="danger"
        :submit-button-label="trans('core/base::system.cleanup.submit_button')"
        :submit-button-attrs="['id' => 'cleanup-submit-action']"
    />
@endsection
