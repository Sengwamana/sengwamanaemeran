<?php

namespace Botble\ACL\Forms;

use Botble\ACL\Http\Requests\CreateUserRequest;
use Botble\ACL\Models\Role;
use Botble\ACL\Models\User;
use Botble\Base\Forms\FieldOptions\EmailFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;

class UserForm extends FormAbstract
{
    public function setup(): void
    {
        $roles = Role::query()->pluck('name', 'id');

        $defaultRole = $roles->where('is_default', 1)->first();

        $this
            ->model(User::class)
            ->setValidatorClass(CreateUserRequest::class)
            ->columns()
            ->add(
                'first_name',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/acl::users.info.first_name'))
                    ->required()
                    ->maxLength(30)
            )
            ->add(
                'last_name',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/acl::users.info.last_name'))
                    ->required()
                    ->maxLength(30)
            )
            ->add(
                'username',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('core/acl::users.username'))
                    ->required()
                    ->maxLength(30)
            )
            ->add('email', TextField::class, EmailFieldOption::make()->required())
            ->add(
                'password',
                'password',
                TextFieldOption::make()
                    ->label(trans('core/acl::users.password'))
                    ->required()
                    ->maxLength(60)
                    ->colspan(2)
            )
            ->add(
                'password_confirmation',
                'password',
                TextFieldOption::make()
                    ->label(trans('core/acl::users.password_confirmation'))
                    ->required()
                    ->maxLength(60)
                    ->colspan(2)
            )
            ->add(
                'role_id',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('core/acl::users.role'))
                    ->choices(['' => trans('core/acl::users.select_role')] + $roles->all())
                    ->when($defaultRole, fn (SelectFieldOption $option) => $option->selected($defaultRole->id))
            )
            ->setBreakFieldPoint('role_id');
    }
}
