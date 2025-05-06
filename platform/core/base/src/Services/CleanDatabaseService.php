<?php

namespace Botble\Base\Services;

use Botble\Setting\Facades\Setting;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CleanDatabaseService
{
    public function getIgnoreTables(): array
    {
        return [
            'migrations',
            'pages',
            'users',
            'activations',
            'settings',
            'translations',
            'widgets',
            'menus',
            'menu_nodes',
        ];
    }

    public function execute(array $except = []): bool
    {
        $except = array_merge($except, $this->getIgnoreTables());

        try {
            $tables = array_map(function (array $table) {
                return $table['name'];
            }, Schema::getTables());

            $tables = array_diff($tables, $except);
        } catch (Throwable) {
            $tables = [];
        }

        if (empty($tables)) {
            return false;
        }

        Schema::disableForeignKeyConstraints();

        foreach (Arr::except($tables, 'settings') as $table) {
            DB::table($table)->truncate();
        }

        Schema::enableForeignKeyConstraints();

        if (in_array('settings', $tables)) {
            Setting::forceDelete(except: [
                'theme',
                'activated_plugins',
                'licensed_to',
                'media_random_hash',
            ]);
        }

        if (in_array('media_files', $tables)) {
            File::cleanDirectory(Storage::disk()->path(''));
        }

        return true;
    }
}
