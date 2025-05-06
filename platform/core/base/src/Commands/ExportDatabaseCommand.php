<?php

namespace Botble\Base\Commands;

use Botble\Base\Facades\BaseHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Process\Process;
use Throwable;

#[AsCommand('cms:db:export', 'Export database to SQL file.')]
class ExportDatabaseCommand extends Command
{
    public function handle(): int
    {
        $config = DB::getConfig();

        switch ($driver = $config['driver']) {
            case 'mysql':
                $sqlPath = $this->argument('output');

                if (! $sqlPath) {
                    $sqlPath = base_path('database.sql');
                }

                try {
                    $command = 'mysqldump --user="%s" --password="%s" --host="%s" --port="%s" "%s" > "%s"';

                    Process::fromShellCommandline(
                        sprintf($command, $config['username'], $config['password'], $config['host'], $config['port'], $config['database'], $sqlPath)
                    )->mustRun();
                } catch (Throwable $exception) {
                    $this->components->error('Failed to export database to SQL file on MySQL connection: ' . $exception->getMessage());

                    BaseHelper::logError($exception);

                    return self::FAILURE;
                }

                $this->components->info('Exported database to SQL file successfully on MySQL connection.');

                return self::SUCCESS;
            case 'pgsql':
                try {
                    $sqlPath = base_path('database.pgsql.dump');

                    $command = 'PGPASSWORD="%s" pg_dump --username="%s" --host="%s" --port="%s" --dbname="%s" -Fc > "%s"';

                    Process::fromShellCommandline(
                        sprintf($command, $config['password'], $config['username'], $config['host'], $config['port'], $config['database'], $sqlPath)
                    )->mustRun();
                } catch (Throwable $exception) {
                    $this->components->error('Failed to export database to SQL file on PostgreSQL connection: ' . $exception->getMessage());

                    BaseHelper::logError($exception);

                    return self::FAILURE;
                }

                $this->components->info('Exported database to SQL file successfully on PostgreSQL connection.');

                return self::SUCCESS;
        }

        $this->components->error(sprintf('The driver [%s] does not support.', $driver));

        return self::FAILURE;
    }

    protected function configure(): void
    {
        $this->addArgument('output', InputArgument::OPTIONAL, 'The SQL file output file path.', 'database.sql');
    }
}
