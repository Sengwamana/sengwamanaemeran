<?php

namespace Botble\ACL\Http\Controllers;

use Botble\ACL\Forms\PasswordForm;
use Botble\ACL\Forms\PreferenceForm;
use Botble\ACL\Forms\ProfileForm;
use Botble\ACL\Forms\UserForm;
use Botble\ACL\Http\Requests\AvatarRequest;
use Botble\ACL\Http\Requests\CreateUserRequest;
use Botble\ACL\Http\Requests\PreferencePatchRequest;
use Botble\ACL\Http\Requests\PreferenceRequest;
use Botble\ACL\Http\Requests\UpdatePasswordRequest;
use Botble\ACL\Http\Requests\UpdateProfileRequest;
use Botble\ACL\Models\User;
use Botble\ACL\Services\ChangePasswordService;
use Botble\ACL\Services\CreateUserService;
use Botble\ACL\Tables\UserTable;
use Botble\Base\Facades\Assets;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseSystemController;
use Botble\Base\Supports\Breadcrumb;
use Botble\Media\Facades\RvMedia;
use Botble\Media\Models\MediaFile;
use Exception;
use Illuminate\Http\Request;
use Throwable;

class UserController extends BaseSystemController
{
    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()
            ->add(
                trans('core/acl::users.users'),
                route('users.index')
            );
    }

    public function index(UserTable $dataTable)
    {
        $this->pageTitle(trans('core/acl::users.users'));

        return $dataTable->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('core/acl::users.create_new_user'));

        return UserForm::create()->renderForm();
    }

    public function store(CreateUserRequest $request, CreateUserService $service)
    {
        $form = UserForm::create();
        $user = null;

        $form->saving(function (UserForm $form) use ($service, $request, &$user): void {
            $user = $service->execute($request);

            $form->setModel($user);
        });

        return $this
            ->httpResponse()
            ->setPreviousRoute('users.index')
            ->setNextUrl($user->url)
            ->withCreatedSuccessMessage();
    }

    public function destroy(User $user)
    {
        return DeleteResourceAction::make($user)
            ->beforeDeleting(function (DeleteResourceAction $action): void {
                $request = $action->getRequest();
                $model = $action->getModel();

                if ($request->user()->is($model)) {
                    throw new Exception(trans('core/acl::users.delete_user_logged_in'));
                }

                if (! $request->user()->isSuperUser() && $model instanceof User && $model->isSuperUser()) {
                    throw new Exception(trans('core/acl::users.cannot_delete_super_user'));
                }
            });
    }

    public function getUserProfile(User $user, Request $request)
    {
        Assets::addScripts('cropper')
            ->addScriptsDirectly('vendor/core/core/acl/js/profile.js');

        $this->pageTitle($user->name);

        $request->route()->setParameter('id', $user->getKey());

        $user->password = null;

        $form = ProfileForm::createFromModel($user)
            ->setUrl(route('users.update-profile', $user->getKey()));

        $passwordForm = PasswordForm::createFromModel($user)
            ->setUrl(route('users.change-password', $user->getKey()));

        $preferenceForm = PreferenceForm::createFromModel($user)
            ->setUrl(route('users.update-preferences', $user->getKey()))
            ->renderForm();

        $currentUser = $request->user();

        $canChangeProfile = $currentUser->hasPermission('users.edit') || $currentUser->getKey() == $user->getKey() || $currentUser->isSuperUser();

        if (! $canChangeProfile) {
            $form->disableFields();
            $form->removeActionButtons();
            $form->setActionButtons(' ');
            $passwordForm->disableFields();
            $passwordForm->removeActionButtons();
            $passwordForm->setActionButtons(' ');
        }

        $form = $form->renderForm();
        $passwordForm = $passwordForm->renderForm();

        return view(
            'core/acl::users.profile.base',
            compact('user', 'form', 'passwordForm', 'canChangeProfile', 'preferenceForm')
        );
    }

    public function postUpdateProfile(User $user, UpdateProfileRequest $request)
    {
        if ($user->email !== $request->input('email')) {
            $users = User::query()
                ->where('email', $request->input('email'))
                ->where('id', '<>', $user->getKey())
                ->exists();

            if ($users) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage(trans('core/acl::users.email_exist'))
                    ->withInput();
            }
        }

        if ($user->username !== $request->input('username')) {
            $users = User::query()
                ->where('username', $request->input('username'))
                ->where('id', '<>', $user->getKey())
                ->exists();

            if ($users) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage(trans('core/acl::users.username_exist'))
                    ->withInput();
            }
        }

        ProfileForm::createFromModel($user)
            ->setRequest($request)
            ->save();

        do_action(USER_ACTION_AFTER_UPDATE_PROFILE, USER_MODULE_SCREEN_NAME, $request, $user);

        return $this
            ->httpResponse()
            ->setMessage(trans('core/acl::users.update_profile_success'));
    }

    public function postChangePassword(User $user, UpdatePasswordRequest $request, ChangePasswordService $service)
    {
        $request->merge(['id' => $user->getKey()]);

        try {
            PasswordForm::createFromModel($user)
                ->saving(function (PasswordForm $form) use ($service, $request) {
                    return tap($service->execute($request), fn ($user) => $form->setupModel($user));
                });
        } catch (Throwable $exception) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($exception->getMessage());
        }

        return $this
            ->httpResponse()
            ->setMessage(trans('core/acl::users.password_update_success'));
    }

    public function updatePreferences(User $user, PreferenceRequest $request)
    {
        PreferenceForm::createFromModel($user)
            ->saving(function (PreferenceForm $form) use ($request): void {
                /**
                 * @var User $model
                 */
                $model = $form->getModel();
                $model->setMeta('locale', $request->input('locale'));
                $model->setMeta('locale_direction', $request->input('locale_direction'));
                $model->setMeta('theme_mode', $request->input('theme_mode'));
            });

        return $this
            ->httpResponse()
            ->setMessage(trans('core/acl::users.update_preferences_success'));
    }

    public function patchUpdatePreferences(User $user, PreferencePatchRequest $request)
    {
        foreach ($request->validated() as $key => $value) {
            $user->setMeta($key, $value);
        }

        $user->save();

        return $this->httpResponse()->withUpdatedSuccessMessage();
    }

    public function postAvatar(User $user, AvatarRequest $request)
    {
        $currentUser = $request->user();

        $hasRightToUpdate = ($currentUser->hasPermission('users.edit') && $currentUser->getKey() === $user->getKey()) || $currentUser->isSuperUser();

        if (! $hasRightToUpdate) {
            return $this
                ->httpResponse()
                ->setNextUrl($user->url)
                ->setError()
                ->setMessage(trans('core/acl::permissions.access_denied_message'));
        }

        try {
            $result = RvMedia::uploadFromBlob($request->file('avatar_file'), folderSlug: 'users');

            if ($result['error']) {
                return $this
                    ->httpResponse()->setError()->setMessage($result['message']);
            }

            $file = $result['data'];

            $mediaFile = MediaFile::query()->find($user->avatar_id);
            $mediaFile?->delete();

            $user->avatar_id = $file->id;
            $user->save();

            return $this
                ->httpResponse()
                ->setMessage(trans('core/acl::users.update_avatar_success'))
                ->setData(['url' => RvMedia::url($file->url)]);
        } catch (Exception $exception) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    public function removeAvatar(User $user, Request $request)
    {
        $currentUser = $request->user();

        $hasRightToUpdate = ($currentUser->hasPermission('users.edit') && $currentUser->getKey() === $user->getKey()) ||
            $currentUser->isSuperUser();

        if (! $hasRightToUpdate) {
            return $this
                ->httpResponse()
                ->setNextUrl($user->url)
                ->setError()
                ->setMessage(trans('core/acl::permissions.access_denied_message'));
        }

        $mediaFile = MediaFile::query()->find($user->avatar_id);

        $mediaFile?->delete();

        $user->avatar_id = null;

        $user->save();

        return $this
            ->httpResponse()
            ->setMessage(trans('core/acl::users.delete_avatar_success'))
            ->setData(['url' => $user->avatar_url]);
    }

    public function makeSuper(User $user)
    {
        try {
            $user->updatePermission(ACL_ROLE_SUPER_USER);
            $user->updatePermission(ACL_ROLE_MANAGE_SUPERS);
            $user->super_user = 1;
            $user->manage_supers = 1;
            $user->save();

            return $this
                ->httpResponse()
                ->setNextRoute('users.index')
                ->setMessage(trans('core/base::system.supper_granted'));
        } catch (Exception $exception) {
            return $this
                ->httpResponse()
                ->setError()
                ->setNextRoute('users.index')
                ->setMessage($exception->getMessage());
        }
    }

    public function removeSuper(User $user, Request $request)
    {
        if ($request->user()->is($user)) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('core/base::system.cannot_revoke_yourself'));
        }

        $user->updatePermission(ACL_ROLE_SUPER_USER, false);
        $user->updatePermission(ACL_ROLE_MANAGE_SUPERS, false);
        $user->super_user = 0;
        $user->manage_supers = 0;
        $user->save();

        return $this
            ->httpResponse()
            ->setNextRoute('users.index')
            ->setMessage(trans('core/base::system.supper_revoked'));
    }
}
