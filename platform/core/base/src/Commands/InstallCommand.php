<?php

namespace Botble\Base\Commands;

use Illuminate\Console\Command;

use function Laravel\Prompts\confirm;

use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('cms:install', 'Install CMS.')]
class InstallCommand extends Command
{
    public function handle(): int
    {
        if (! confirm('Do you want to proceed with installation?')) {
            return self::SUCCESS;
        }

        $this->components->info('Starting installation...');

        $this->components->info('Running migrate...');
        $this->call('migrate:fresh');
        $this->components->info('Migrate done!');

        if (confirm('Create a new super user?')) {
            $this->call('cms:user:create');
        }

        if (confirm('Do you want to activate all plugins?')) {
            $this->components->info('Activating all plugins...');
            $this->call('cms:plugin:activate:all');
            $this->components->info('All plugins are activated!');
        }

        if (confirm('Do you want to install sample data?')) {
            $this->components->info('Seeding...');
            $this->call('db:seed');
            $this->components->info('Seeding done!');
        }

        $this->components->info('Publishing assets...');
        $this->call('cms:publish:assets');
        $this->components->info('Publishing assets done!');

        $this->components->info('Your CMS is ready to use!');

        return self::SUCCESS;
    }
}
