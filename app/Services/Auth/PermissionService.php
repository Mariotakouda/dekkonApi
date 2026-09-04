<?php

namespace App\Services\Auth;

use App\Models\Role;
use App\Models\User;

class PermissionService
{
    public function syncRolePermissions(Role $role, array $permissionIds): Role
    {
        $role->permissions()->sync($permissionIds);

        return $role->fresh('permissions');
    }

    public function grantDirectPermission(User $user, string $permissionId): void
    {
        $user->directPermissions()->syncWithoutDetaching([$permissionId]);
    }

    public function revokeDirectPermission(User $user, string $permissionId): void
    {
        $user->directPermissions()->detach($permissionId);
    }

    public function allPermissionCodes(User $user): array
    {
        $rolePermissions = $user->role?->permissions->pluck('code')->toArray() ?? [];
        $directPermissions = $user->directPermissions->pluck('code')->toArray();

        return array_unique(array_merge($rolePermissions, $directPermissions));
    }
}
