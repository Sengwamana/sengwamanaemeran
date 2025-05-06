<?php

namespace Botble\Installer\Http\Controllers;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Supports\Core;
use Botble\Installer\Events\EnvironmentSaved;
use Botble\Installer\Http\Requests\SaveEnvironmentRequest;
use Botble\Installer\InstallerStep\InstallerStep;
use Botble\Installer\Services\ImportDatabaseService;
use Botble\Installer\Supports\EnvironmentManager;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;

class EnvironmentController extends BaseController
{
    public function index(Request $request): View|RedirectResponse
    {
        if (! URL::hasValidSignature($request)) {
            return redirect()->route('installers.requirements.index');
        }

        return view('packages/installer::environment');
    }

    public function store(
        SaveEnvironmentRequest $request,
        EnvironmentManager $environmentManager,
        ImportDatabaseService $importDatabaseService
    ): RedirectResponse {
        $driverName = $request->input('database_connection');
        $connectionName = 'database.connections.' . $driverName;
        $databaseName = $request->input('database_name');

        config([
            'database.default' => $driverName,
            $connectionName => array_merge(config($connectionName), [
                'host' => $request->input('database_hostname'),
                'port' => $request->input('database_port'),
                'database' => $databaseName,
                'username' => $request->input('database_username'),
                'password' => $request->input('database_password'),
            ]),
        ]);

        $results = $environmentManager->save($request);

        event(new EnvironmentSaved($request));

        if (InstallerStep::hasMoreThemes()) {
            $nextRouteName = 'installers.themes.index';
        } else {
            $nextRouteName = 'installers.accounts.index';

            $databaseFilePath = base_path('database.sql');

            if (File::exists($databaseFilePath) && File::size($databaseFilePath) > 1024) {
                $importDatabaseService->handle($databaseFilePath);
            } else {
                if (! Schema::hasTable('migrations')) {
                    Schema::create('migrations', function ($table): void {
                        // The migrations table is responsible for keeping track of which of the
                        // migrations have actually run for the application. We'll create the
                        // table to hold the migration file's path as well as the batch ID.
                        $table->increments('id');
                        $table->string('migration');
                        $table->integer('batch');
                    });
                }

                Core::make()->runMigrationFiles();
            }
        }

        BaseHelper::saveFileData(storage_path(INSTALLING_SESSION_NAME), Carbon::now()->toDateTimeString());

        return redirect()
            ->to(URL::temporarySignedRoute($nextRouteName, Carbon::now()->addMinutes(30)))
            ->with('install_message', $results);
    }
}
