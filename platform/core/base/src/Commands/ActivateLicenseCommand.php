<?php

namespace Botble\Base\Commands;

use Botble\Base\Commands\Traits\ValidateCommandInput;
use Botble\Base\Exceptions\LicenseIsAlreadyActivatedException;
use Botble\Base\Supports\Core;
use Botble\Setting\Facades\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\text;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;
use Throwable;

#[AsCommand('cms:license:activate', 'Activate license')]
class ActivateLicenseCommand extends Command
{
    use ValidateCommandInput;

    public function __construct(protected Core $core)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        if ($this->option('buyer') && $this->option('purchase_code')) {
            $username = $this->option('buyer');
            $purchasedCode = $this->option('purchase_code');
            $validator = Validator::make(
                [
                    'buyer' => $username,
                    'purchase_code' => $purchasedCode,
                ],
                [
                    'buyer' => ['required', 'string', 'min:2', 'max:60'],
                    'purchase_code' => ['required', 'string', 'min:19', 'max:36'],
                ]
            )->stopOnFirstFailure();

            if ($validator->fails()) {
                $this->components->error($validator->messages()->first());

                return self::FAILURE;
            }
        } else {
            $username = text(
                label: 'Envato username',
                required: true,
                validate: $this->validate('string|min:2|max:60'),
            );

            if (filter_var($username, FILTER_VALIDATE_URL)) {
                $this->components->error(
                    sprintf(
                        'Envato username must not a URL. Please try "%s".',
                        explode('/', $username)[count(explode('/', $username)) - 1]
                    )
                );

                return self::FAILURE;
            }

            $purchasedCode = text(
                label: 'Purchase code',
                required: true,
                validate: $this->validate('string|min:19|max:36'),
            );
        }

        try {
            return $this->performUpdate($purchasedCode, $username);
        } catch (LicenseIsAlreadyActivatedException) {
            $this->core->revokeLicense($purchasedCode, $username);

            return tap(
                $this->performUpdate($purchasedCode, $username),
                fn () => $this->components->warn('Your license on the previous domain has been revoked!')
            );
        } catch (Throwable $exception) {
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        }
    }

    protected function performUpdate(string $purchasedCode, string $username): int
    {
        $status = $this->core->activateLicense($purchasedCode, $username);

        if (! $status) {
            $this->components->error('This license is invalid.');

            return self::FAILURE;
        }

        Setting::forceSet('licensed_to', $username)->save();

        $this->components->info('This license has been activated successfully.');

        return self::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addOption('buyer', null, InputOption::VALUE_REQUIRED, 'The buyer name');
        $this->addOption('purchase_code', null, InputOption::VALUE_REQUIRED, 'The purchase code');
    }
}
