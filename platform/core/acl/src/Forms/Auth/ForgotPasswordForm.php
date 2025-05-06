<?php

namespace Botble\ACL\Forms\Auth;

use Botble\ACL\Http\Requests\ForgotPasswordRequest;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\TextField;

class ForgotPasswordForm extends AuthForm
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->setValidatorClass(ForgotPasswordRequest::class)
            ->setUrl(route('access.password.email'))
            ->heading(trans('core/acl::auth.forgot_password.title'))
            ->add('description', HtmlField::class, [
                'html' => BaseHelper::clean(trans('core/acl::auth.forgot_password.message')),
            ])
            ->add(
                'email',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/acl::auth.login.email'))
                    ->value(old('email'))
                    ->placeholder(trans('core/acl::auth.login.placeholder.email'))
                    ->required()
            )
            ->submitButton(trans('core/acl::auth.forgot_password.submit'), 'ti ti-mail')
            ->add(
                'back_to_login',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->content(
                        sprintf(
                            '<div class="mt-3 text-center"><a href="%s">%s</a></div>',
                            route('access.login'),
                            trans('core/acl::auth.back_to_login')
                        )
                    )
            );
    }
}
