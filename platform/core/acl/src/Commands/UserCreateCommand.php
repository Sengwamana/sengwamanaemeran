<?php

namespace Botble\ACL\Commands;

use Botble\ACL\Models\User;
use Botble\ACL\Services\ActivateUserService;
use Botble\Base\Commands\Traits\ValidateCommandInput;
use Botble\Base\Supports\Helper;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Auth\Authenticatable;

use Illuminate\Validation\Rule;

use function Laravel\Prompts\{password, text};

use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand('cms:user:create', 'Create a super user')]
class UserCreateCommand extends Command
{
    use ValidateCommandInput;

    public function handle(ActivateUserService $activateUserService): int
    {
        try {
            $user = $this->createUser();

            if ($activateUserService->activate($user)) {
                $this->sendSuccessMessage($user);
            }

            Helper::clearCache();

            return self::SUCCESS;
        } catch (Exception $exception) {
            $this->components->error('User could not be created.');
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        }
    }

    protected function getUserData(): array
    {
        return [
            'first_name' => text(
                label: 'First name',
                required: true,
                validate: $this->validate([
                    'required',
                    'min:2',
                    'max:60',
                ]),
            ),
            'last_name' => text(
                label: 'Last name',
                required: true,
                validate: $this->validate([
                    'required',
                    'min:2',
                    'max:60',
                ]),
            ),
            'email' => text(
                label: 'Email address',
                required: true,
                validate: $this->validate([
                    'email',
                    'max:60',
                    Rule::unique((new User())->getTable(), 'email'),
                ])
            ),
            'username' => text(
                label: 'Username',
                required: true,
                validate: $this->validate([
                    'min:4',
                    'max:60',
                    Rule::unique((new User())->getTable(), 'username'),
                ])
            ),
            'password' => password(
                label: 'Password',
                required: true,
                validate: $this->validate([
                    'min:6',
                    'max:60',
                ])
            ),
        ];
    }

    protected function createUser(): User
    {
        /** @var User $user */
        $user = User::query()->forceCreate([
            ...$this->getUserData(),
            'super_user' => true,
            'manage_supers' => true,
        ]);

        return $user;
    }

    protected function sendSuccessMessage(Authenticatable $user): void
    {
        $this->components->info(sprintf(
            'Super user %s has been created. You can login at %s',
            $user->getAttribute('email') ?? $user->getAttribute('username'),
            route('access.login')
        ));
    }
}
