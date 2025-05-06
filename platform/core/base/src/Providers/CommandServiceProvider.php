<?php

namespace Botble\Base\Providers;

use Botble\Base\Commands\ActivateLicenseCommand;
use Botble\Base\Commands\CleanupSystemCommand;
use Botble\Base\Commands\ClearExpiredCacheCommand;
use Botble\Base\Commands\ClearLogCommand;
use Botble\Base\Commands\ExportDatabaseCommand;
use Botble\Base\Commands\FetchGoogleFontsCommand;
use Botble\Base\Commands\GoogleFontsUpdateCommand;
use Botble\Base\Commands\ImportDatabaseCommand;
use Botble\Base\Commands\InstallCommand;
use Botble\Base\Commands\PublishAssetsCommand;
use Botble\Base\Commands\UpdateCommand;
use Botble\Base\Supports\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\AboutCommand;

class CommandServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            ActivateLicenseCommand::class,
            CleanupSystemCommand::class,
            ClearExpiredCacheCommand::class,
            ClearLogCommand::class,
            ExportDatabaseCommand::class,
            FetchGoogleFontsCommand::class,
            ImportDatabaseCommand::class,
            InstallCommand::class,
            PublishAssetsCommand::class,
            UpdateCommand::class,
            GoogleFontsUpdateCommand::class,
        ]);

        AboutCommand::add('Core Information', fn () => [
            'CMS Version' => get_cms_version(),
            'Core Version' => get_core_version(),
        ]);

        $this->app->afterResolving(Schedule::class, function (Schedule $schedule): void {
            $schedule->command(ClearExpiredCacheCommand::class)->everyFiveMinutes();
        });
    }
}
