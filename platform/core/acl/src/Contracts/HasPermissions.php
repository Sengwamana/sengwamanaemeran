<?php

namespace Botble\ACL\Contracts;

interface HasPermissions
{
    public function updatePermission(string $permission, bool $value = true, bool $create = false): static;

    public function addPermission(string $permission, bool $value = true): static;

    public function removePermission(string $permission): static;

    public function hasPermission(string|array $permissions): bool;

    public function hasAnyPermission(string|array $permissions): bool;
}
