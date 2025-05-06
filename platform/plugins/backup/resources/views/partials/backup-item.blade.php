<tr>
    <td>{{ $data['name'] }}</td>
    <td>
        @if ($data['description'])
            {{ $data['description'] }}
        @else
            &mdash;
        @endif
    </td>
    <td style="width: 250px;">{{ BaseHelper::humanFilesize(get_backup_size($key)) }}</td>
    <td style="width: 200px;">{{ $data['date'] }}</td>
    <td>
        <div class="btn-list justify-content-end">
            @if ($backupManager->isDatabaseBackupAvailable($key))
                <x-core::button
                    tag="a"
                    size="sm"
                    :href="route('backups.download.database', $key)"
                    :tooltip="trans('plugins/backup::backup.download_database')"
                    icon="ti ti-database"
                    :icon-only="true"
                    color="success"
                />
            @endif

            @if ($backupManager->isStorageBackupAvailable($key))
                <x-core::button
                    tag="a"
                    size="sm"
                    :href="route('backups.download.uploads.folder', $key)"
                    :tooltip="trans('plugins/backup::backup.download_uploads_folder')"
                    icon="ti ti-download"
                    :icon-only="true"
                    color="primary"
                />
            @endif

            @if (
                $driver === 'mysql' &&
                    auth()->guard()->user()->hasPermission('backups.restore'))
                <x-core::button
                    size="sm"
                    :tooltip="trans('plugins/backup::backup.restore_tooltip')"
                    icon="ti ti-reload"
                    :icon-only="true"
                    color="info"
                    class="restoreBackup"
                    :data-section="route('backups.restore', $key)"
                />
            @endif

            @if (auth()->guard()->user()->hasPermission('backups.destroy'))
                <x-core::button
                    size="sm"
                    :tooltip="trans('core/base::tables.delete_entry')"
                    icon="ti ti-trash"
                    :icon-only="true"
                    color="danger"
                    class="deleteDialog"
                    :data-section="route('backups.destroy', $key)"
                />
            @endif
        </div>
    </td>
</tr>
