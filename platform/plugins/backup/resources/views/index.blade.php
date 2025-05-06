@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    @if (!function_exists('proc_open'))
        <x-core::alert
            type="warning"
            :title="trans('plugins/backup::backup.proc_open_disabled_error')"
        />
    @endif

    @if ($driver === 'mysql')
        <x-core::alert type="warning">
            <p>- {!! BaseHelper::clean(trans('plugins/backup::backup.important_message1')) !!}</p>
            <p>- {!! BaseHelper::clean(trans('plugins/backup::backup.important_message2')) !!}</p>
            <p>- {!! BaseHelper::clean(trans('plugins/backup::backup.important_message3')) !!}</p>
            <p>- {!! BaseHelper::clean(trans('plugins/backup::backup.important_message4')) !!}</p>
        </x-core::alert>
    @elseif ($driver === 'pgsql')
        <x-core::alert type="warning">
            <p>- {!! BaseHelper::clean(trans('plugins/backup::backup.important_message_pgsql1')) !!}</p>
            <p>- {!! BaseHelper::clean(trans('plugins/backup::backup.important_message_pgsql2')) !!}</p>
        </x-core::alert>
    @endif

    <x-core::card>
        @if (
            $driver === 'mysql' &&
                auth()->user()->hasPermission('backups.create'))
            <x-core::card.header>
                <x-core::card.actions>
                    <x-core::button
                        type="button"
                        color="primary"
                        id="generate_backup"
                    >
                        {{ trans('plugins/backup::backup.generate_btn') }}
                    </x-core::button>
                </x-core::card.actions>
            </x-core::card.header>
        @endif
        <div class="table-responsive">
            <table
                class="card-table table table-striped"
                id="table-backups"
            >
                <thead>
                    <tr>
                        <th>{{ trans('core/base::tables.name') }}</th>
                        <th>{{ trans('core/base::tables.description') }}</th>
                        <th>{{ trans('plugins/backup::backup.size') }}</th>
                        <th>{{ trans('core/base::tables.created_at') }}</th>
                        <th class="text-end">{{ trans('core/table::table.operations') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($backups as $key => $backup)
                        @include('plugins/backup::partials.backup-item', [
                            'data' => $backup,
                            'backupManager' => $backupManager,
                            'key' => $key,
                        ])
                    @empty
                        <tr class="text-center no-backup-row">
                            <td colspan="5">{{ trans('plugins/backup::backup.no_backups') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-core::card>

    @if (auth()->user()->hasPermission('backups.create'))
        <x-core::modal
            id="create-backup-modal"
            type="info"
            :title="trans('plugins/backup::backup.create')"
            :form-action="route('backups.create')"
        >
            <x-core::form.text-input
                :label="trans('core/base::forms.name')"
                name="name"
                :placeholder="trans('core/base::forms.name')"
                data-counter="120"
                :required="true"
            />

            <x-core::form.textarea
                :label="trans('core/base::forms.description')"
                rows="4"
                name="description"
                :placeholder="trans('core/base::forms.description')"
                data-counter="400"
            />

            <x-core::form.on-off.checkbox
                :label="trans('plugins/backup::backup.backup_only_db')"
                :helperText="trans('plugins/backup::backup.backup_only_db_helper')"
                name="backup_only_db"
                :checked="false"
            />

            <x-slot:footer>
                <x-core::button
                    type="submit"
                    color="primary"
                    id="create-backup-button"
                    class="ms-auto"
                >
                    {{ trans('plugins/backup::backup.create_btn') }}
                </x-core::button>
            </x-slot:footer>
        </x-core::modal>
    @endif

    @if (auth()->user()->hasPermission('backups.restore'))
        <x-core::modal.action
            id="restore-backup-modal"
            type="info"
            :title="trans('plugins/backup::backup.restore')"
            :description="trans('plugins/backup::backup.restore_confirm_msg')"
            :submit-button-label="trans('plugins/backup::backup.restore_btn')"
            :submit-button-attrs="['id' => 'restore-backup-button']"
        />
    @endif

    <x-core::modal.action
        type="danger"
        class="modal-confirm-delete"
        :title="trans('core/base::tables.confirm_delete')"
        :description="trans('core/base::tables.confirm_delete_msg')"
        :submit-button-label="trans('core/base::tables.delete')"
        :submit-button-attrs="['class' => 'delete-crud-entry']"
    />
@endsection
