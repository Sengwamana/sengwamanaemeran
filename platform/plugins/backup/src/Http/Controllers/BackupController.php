<?php

namespace Botble\Backup\Http\Controllers;

use Botble\Backup\Http\Requests\BackupRequest;
use Botble\Backup\Supports\Backup;
use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Controllers\BaseSystemController;
use Botble\Base\Supports\Helper;
use Botble\Media\Facades\RvMedia;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BackupController extends BaseSystemController
{
    protected string $databaseDriver;

    public function __construct(protected Backup $backup)
    {
        $this->databaseDriver = DB::getConfig('driver');
    }

    public function getIndex()
    {
        $this->pageTitle(trans('plugins/backup::backup.menu_name'));

        Assets::addScriptsDirectly(['vendor/core/plugins/backup/js/backup.js'])
            ->addStylesDirectly(['vendor/core/plugins/backup/css/backup.css']);

        $backupManager = $this->backup;

        $backups = $this->backup->getBackupList();

        $driver = $this->databaseDriver;

        return view('plugins/backup::index', compact('backups', 'backupManager', 'driver'));
    }

    public function store(BackupRequest $request)
    {
        if ($this->databaseDriver !== 'mysql') {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('plugins/backup::backup.database_driver_not_supported'));
        }

        try {
            BaseHelper::maximumExecutionTimeAndMemoryLimit();

            $backupOnlyDb = $request->boolean('backup_only_db');

            $key = Carbon::now()->format('Y-m-d-H-i-s');

            $data = $this->backup->createBackupFolder($request->input('name'), $request->input('description'));
            $this->backup->backupDb($key);

            if (! $backupOnlyDb) {
                $this->backup->backupFolder(RvMedia::getUploadPath(), $key);
            }

            do_action(BACKUP_ACTION_AFTER_BACKUP, BACKUP_MODULE_SCREEN_NAME, $request);

            $data['backupManager'] = $this->backup;
            $data['driver'] = $this->databaseDriver;

            return $this
                ->httpResponse()
                ->setData(view('plugins/backup::partials.backup-item', $data)->render())
                ->setMessage(trans('plugins/backup::backup.create_backup_success'));
        } catch (Exception $exception) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function destroy(string $folder)
    {
        try {
            $this->backup->deleteFolderBackup($this->backup->getBackupPath($folder));

            return $this
                ->httpResponse()
                ->setMessage(trans('plugins/backup::backup.delete_backup_success'));
        } catch (Exception $exception) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function getRestore(string $folder, Request $request)
    {
        if ($this->databaseDriver !== 'mysql') {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('plugins/backup::backup.database_driver_not_supported'));
        }

        try {
            $path = $this->backup->getBackupPath($folder);

            $hasSQL = false;

            foreach (BaseHelper::scanFolder($path) as $file) {
                if (Str::contains(basename($file), 'database')) {
                    $hasSQL = true;
                    $this->backup->restoreDatabase($path . DIRECTORY_SEPARATOR . $file, $path);
                }
            }

            if (! $hasSQL) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage(trans('plugins/backup::backup.cannot_restore_database'));
            }

            foreach (BaseHelper::scanFolder($path) as $file) {
                if (Str::contains(basename($file), 'storage')) {
                    $pathTo = config('filesystems.disks.public.root');
                    $this->backup->cleanDirectory($pathTo);
                    $this->backup->extractFileTo($path . DIRECTORY_SEPARATOR . $file, $pathTo);
                }
            }

            setting()->forceSet('media_random_hash', md5((string) time()))->save();

            Helper::clearCache();

            do_action(BACKUP_ACTION_AFTER_RESTORE, BACKUP_MODULE_SCREEN_NAME, $request);

            return $this
                ->httpResponse()
                ->setMessage(trans('plugins/backup::backup.restore_backup_success'));
        } catch (Exception $exception) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function getDownloadDatabase(string $folder)
    {
        $path = $this->backup->getBackupPath($folder);

        foreach (BaseHelper::scanFolder($path) as $file) {
            if (Str::contains(basename($file), 'database')) {
                return response()->download($path . DIRECTORY_SEPARATOR . $file);
            }
        }

        return $this
            ->httpResponse()
            ->setError()
            ->setMessage(trans('plugins/backup::backup.database_backup_not_existed'));
    }

    public function getDownloadUploadFolder(string $folder)
    {
        $path = $this->backup->getBackupPath($folder);

        foreach (BaseHelper::scanFolder($path) as $file) {
            if (Str::contains(basename($file), 'storage')) {
                return response()->download($path . DIRECTORY_SEPARATOR . $file);
            }
        }

        return $this
            ->httpResponse()
            ->setError()
            ->setMessage(trans('plugins/backup::backup.uploads_folder_backup_not_existed'));
    }
}
