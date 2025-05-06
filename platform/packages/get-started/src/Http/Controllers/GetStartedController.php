<?php

namespace Botble\GetStarted\Http\Controllers;

use Botble\ACL\Models\User;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\GetStarted\Http\Requests\GetStartedRequest;
use Botble\Setting\Facades\Setting;
use Botble\Theme\Facades\ThemeOption;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class GetStartedController extends BaseController
{
    public function save(GetStartedRequest $request): BaseHttpResponse
    {
        $step = $request->input('step');

        $nextStep = $step + 1;

        switch ($step) {
            case 1:
                break;
            case 2:
                if (! theme_option()->hasField('primary_color')) {
                    $request->request->remove('primary_color');
                }

                if (! theme_option()->hasField('primary_font')) {
                    $request->request->remove('primary_font');
                }

                foreach ($request->except(['_token', 'step']) as $key => $value) {
                    if ($value === null) {
                        continue;
                    }

                    if (is_array($value)) {
                        $value = json_encode($value);
                    }

                    ThemeOption::setOption($key, $value);

                    if (in_array($key, ['admin_logo', 'admin_favicon'])) {
                        Setting::set($key, $value);
                    }
                }

                ThemeOption::saveOptions();

                Setting::save();

                $user = Auth::guard()->user();

                $defaultUsername = config('core.base.general.demo.account.username');
                $defaultPassword = config('core.base.general.demo.account.password');

                if (
                    $defaultUsername &&
                    $defaultPassword &&
                    $user->username != $defaultUsername &&
                    ! Hash::check($defaultPassword, $user->getAuthPassword())
                ) {
                    $nextStep = 4;
                }

                break;
            case 3:
                $user = Auth::guard()->user();

                $email = $request->input('email');

                if ($user->email !== $email) {
                    $users = User::query()
                        ->where('email', $email)
                        ->where('id', '<>', $user->id)
                        ->exists();

                    if ($users) {
                        return $this
                            ->httpResponse()
                            ->setError()
                            ->setMessage(trans('core/acl::users.email_exist'))
                            ->withInput();
                    }
                }

                $username = $request->input('username');

                if ($user->username !== $username) {
                    $users = User::query()
                        ->where('username', $username)
                        ->where('id', '<>', $user->id)
                        ->exists();

                    if ($users) {
                        return $this
                            ->httpResponse()
                            ->setError()
                            ->setMessage(trans('core/acl::users.username_exist'))
                            ->withInput();
                    }
                }

                $user->fill($request->only(['username', 'email']));
                $user->password = Hash::make($request->input('password'));

                $user->save();

                do_action(USER_ACTION_AFTER_UPDATE_PROFILE, USER_MODULE_SCREEN_NAME, $request, $user);
                do_action(USER_ACTION_AFTER_UPDATE_PASSWORD, USER_MODULE_SCREEN_NAME, $request, $user);

                event(new UpdatedContentEvent(USER_MODULE_SCREEN_NAME, $request, $user));

                break;
            case 4:
                Setting::set('is_completed_get_started', '1')->save();

                break;
        }

        return $this
            ->httpResponse()
            ->setData(['step' => $nextStep]);
    }
}
