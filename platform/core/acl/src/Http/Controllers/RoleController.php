<?php

namespace Botble\ACL\Http\Controllers;

use Botble\ACL\Events\RoleAssignmentEvent;
use Botble\ACL\Events\RoleUpdateEvent;
use Botble\ACL\Forms\RoleForm;
use Botble\ACL\Http\Requests\AssignRoleRequest;
use Botble\ACL\Http\Requests\RoleCreateRequest;
use Botble\ACL\Models\Role;
use Botble\ACL\Models\User;
use Botble\ACL\Tables\RoleTable;
use Botble\Base\Http\Controllers\BaseSystemController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Supports\Breadcrumb;
use Botble\Base\Supports\Helper;

class RoleController extends BaseSystemController
{
    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()
            ->add(
                trans('core/acl::permissions.role_permission'),
                route('roles.index')
            );
    }

    public function index(RoleTable $dataTable)
    {
        $this->pageTitle(trans('core/acl::permissions.role_permission'));

        return $dataTable->renderTable();
    }

    public function destroy(Role $role)
    {
        $role->delete();

        Helper::clearCache();

        return $this
            ->httpResponse()
            ->setMessage(trans('core/acl::permissions.delete_success'));
    }

    public function edit(Role $role)
    {
        $this->pageTitle(trans('core/acl::permissions.details', ['name' => $role->name]));

        return RoleForm::createFromModel($role)->renderForm();
    }

    public function update(Role $role, RoleCreateRequest $request)
    {
        if ($request->input('is_default')) {
            Role::query()->where('id', '!=', $role->getKey())->update(['is_default' => 0]);
        }

        $role->name = $request->input('name');
        $role->permissions = $this->cleanPermission((array) $request->input('flags', []));
        $role->description = $request->input('description');
        $role->updated_by = $request->user()->getKey();
        $role->is_default = $request->input('is_default');
        $role->save();

        Helper::clearCache();

        event(new RoleUpdateEvent($role));

        return $this
            ->httpResponse()
            ->setPreviousRoute('roles.index')
            ->setNextRoute('roles.edit', $role->getKey())
            ->setMessage(trans('core/acl::permissions.modified_success'));
    }

    protected function cleanPermission(array $permissions): array
    {
        if (! $permissions) {
            return [];
        }

        $cleanedPermissions = [];
        foreach ($permissions as $permissionName) {
            $cleanedPermissions[$permissionName] = true;
        }

        return $cleanedPermissions;
    }

    public function create()
    {
        $this->pageTitle(trans('core/acl::permissions.create_role'));

        return RoleForm::create()->renderForm();
    }

    public function store(RoleCreateRequest $request)
    {
        if ($request->input('is_default')) {
            Role::query()->update(['is_default' => 0]);
        }

        $role = Role::query()->create([
            'name' => $request->input('name'),
            'permissions' => $this->cleanPermission((array) $request->input('flags', [])),
            'description' => $request->input('description'),
            'is_default' => $request->input('is_default'),
            'created_by' => $request->user()->getKey(),
            'updated_by' => $request->user()->getKey(),
        ]);

        return $this
            ->httpResponse()
            ->setPreviousRoute('roles.index')
            ->setNextRoute('roles.edit', $role->getKey())
            ->setMessage(trans('core/acl::permissions.create_success'));
    }

    public function getDuplicate(Role $role)
    {
        $duplicatedRole = Role::query()->create([
            'name' => $role->name . ' (Duplicate)',
            'slug' => $role->slug,
            'permissions' => $role->permissions,
            'description' => $role->description,
            'created_by' => $role->created_by,
            'updated_by' => $role->updated_by,
        ]);

        return $this->httpResponse()
            ->setPreviousRoute('roles.edit', $role->getKey())
            ->setNextRoute('roles.edit', $duplicatedRole->getKey())
            ->setMessage(trans('core/acl::permissions.duplicated_success'));
    }

    public function getJson(): array
    {
        $pl = [];
        foreach (Role::query()->get() as $role) {
            $pl[] = [
                'value' => $role->getKey(),
                'text' => $role->name,
            ];
        }

        return $pl;
    }

    public function postAssignMember(AssignRoleRequest $request): BaseHttpResponse
    {
        /**
         * @var User $user
         */
        $user = User::query()->findOrFail($request->input('pk'));

        /**
         * @var Role $role
         */
        $role = Role::query()->findOrFail($request->input('value'));

        $user->roles()->sync([$role->getKey()]);

        event(new RoleAssignmentEvent($role, $user));

        return $this->httpResponse();
    }
}
