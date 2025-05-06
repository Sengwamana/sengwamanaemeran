<?php

namespace Botble\ACL\Forms\Auth;

use Botble\ACL\Http\Requests\ResetPasswordRequest;
use Botble\Base\Forms\FieldOptions\TextFieldOption;

class ResetPasswordForm extends AuthForm
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->setValidatorClass(ResetPasswordRequest::class)
            ->setUrl(route('access.password.reset.post'))
            ->heading(trans('core/acl::auth.reset_password'))
            ->add('token', 'hidden', [
                'value' => $this->request->route('token'),
            ])
            ->add(
                'email',
                'email',
                TextFieldOption::make()
                    ->label(trans('core/acl::auth.reset.email'))
                    ->value(old('email', $this->request->input('email')))
                    ->placeholder(trans('core/acl::auth.login.placeholder.email'))
                    ->required()
            )
            ->add(
                'password',
                'password',
                TextFieldOption::make()
                ->label(trans('core/acl::auth.reset.new_password'))
                ->required()
                ->placeholder(trans('core/acl::auth.reset.placeholder.new_password'))
            )
            ->add(
                'password_confirmation',
                'password',
                TextFieldOption::make()
                    ->label(trans('core/acl::auth.reset.password_confirmation'))
                    ->required()
                    ->placeholder(trans('core/acl::auth.reset.placeholder.new_password_confirmation'))
            )
            ->submitButton(trans('core/acl::auth.reset.update'));
    }
}
