<?php

namespace Botble\Base\Commands;

use Botble\Base\Services\CleanDatabaseService;
use Exception;
use Illuminate\Console\Command;

use function Laravel\Prompts\confirm;

use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('cms:system:cleanup', 'All the preloaded data will be deleted from the database except few mandatory record that is essential for running the software properly.')]
class CleanupSystemCommand extends Command
{
    public function handle(CleanDatabaseService $cleanDatabaseService): int
    {
        try {
            if (! confirm('Are you sure you want to cleanup your database?', false)) {
                return self::FAILURE;
            }

            $this->components->task('Cleaning database', fn () => $cleanDatabaseService->execute());

            $this->components->info('Cleaned database successfully.');
        } catch (Exception $exception) {
            $this->components->error($exception->getMessage());
        }

        return self::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addOption('force', 'f', null, 'Cleanup database without confirmation');
    }
}
