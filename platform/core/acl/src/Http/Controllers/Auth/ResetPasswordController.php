<?php

namespace Botble\ACL\Http\Controllers\Auth;

use Botble\ACL\Forms\Auth\ResetPasswordForm;
use Botble\ACL\Traits\ResetsPasswords;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;

class ResetPasswordController extends BaseController
{
    use ResetsPasswords;

    protected string $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest');

        $this->redirectTo = route('dashboard.index');
    }

    public function showResetForm(Request $request, $token = null)
    {
        $this->pageTitle(trans('core/acl::auth.reset.title'));

        return ResetPasswordForm::create()->renderForm();
    }
}
