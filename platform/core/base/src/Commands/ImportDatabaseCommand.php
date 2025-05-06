<?php

namespace Botble\Base\Commands;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Services\ClearCacheService;
use Botble\Base\Supports\Database;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

use function Laravel\Prompts\confirm;

use Symfony\Component\Console\Attribute\AsCommand;

use Symfony\Component\Console\Input\InputArgument;

#[AsCommand('cms:db:import', 'Import database from SQL file.')]
class ImportDatabaseCommand extends Command
{
    public function handle(): int
    {
        BaseHelper::maximumExecutionTimeAndMemoryLimit();

        if (Schema::hasTable('settings') && confirm('Do you want to clear all tables in your database?', false)) {
            $this->components->info('Dropping all tables...');

            $this->call('db:wipe');
        }

        $fileName = $this->argument('file');

        if (str_contains($fileName, DIRECTORY_SEPARATOR)) {
            $filePath = $fileName;
        } else {
            $filePath = base_path($fileName);
        }

        if (! File::exists($filePath)) {
            $this->components->error('The SQL file does not exist.');

            return self::FAILURE;
        }

        $config = DB::getConfig();

        if (($driver = $config['driver']) == 'mysql') {
            $this->components->task(
                'Importing database from SQL file',
                fn () => Database::restoreFromPath($filePath)
            );

            $this->components->task(
                'Clearing cache',
                fn () => ClearCacheService::make()->purgeAll()
            );

            $this->components->info('Imported successfully.');

            return self::SUCCESS;
        }

        $this->components->error(sprintf('The driver [%s] does not support.', $driver));

        return self::FAILURE;
    }

    protected function configure(): void
    {
        $this->addArgument('file', InputArgument::OPTIONAL, 'The SQL file to import.', 'database.sql');
    }
}
