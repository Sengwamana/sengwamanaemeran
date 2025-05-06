<?php

namespace Botble\RequestLog\Providers;

use Botble\Base\Supports\ServiceProvider;
use Botble\RequestLog\Commands\RequestLogClearCommand;
use Botble\RequestLog\Models\RequestLog;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Console\PruneCommand;

class CommandServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->commands([
            RequestLogClearCommand::class,
        ]);

        $this->app->afterResolving(Schedule::class, function (Schedule $schedule): void {
            $schedule->command(PruneCommand::class, ['--model' => RequestLog::class])->dailyAt('00:30');
        });
    }
}
