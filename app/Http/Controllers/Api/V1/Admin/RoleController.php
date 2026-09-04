<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AssignPermissionRequest;
use App\Http\Requests\Admin\StoreRoleRequest;
use App\Http\Resources\Admin\RoleResource;
use App\Models\Role;
use App\Traits\ApiResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    use ApiResponder;

    public function index(): JsonResponse
    {
        $roles = Role::withCount('users')->with('permissions')->orderBy('name')->get();

        return $this->success(RoleResource::collection($roles));
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = DB::transaction(function () use ($request) {
            $role = Role::create($request->only(['name', 'code', 'description']));

            if ($request->filled('permission_ids')) {
                $role->permissions()->sync($request->permission_ids);
            }

            return $role;
        });

        return $this->success(new RoleResource($role->load('permissions')), 'Rôle créé.', 201);
    }

    public function show(Role $role): JsonResponse
    {
        return $this->success(new RoleResource($role->load('permissions')));
    }

    public function update(StoreRoleRequest $request, Role $role): JsonResponse
    {
        abort_if($role->is_system, 403, 'Ce rôle système ne peut pas être modifié.');

        $role->update($request->only(['name', 'description']));

        return $this->success(new RoleResource($role->fresh('permissions')), 'Rôle mis à jour.');
    }

    public function syncPermissions(AssignPermissionRequest $request, Role $role): JsonResponse
    {
        $role->permissions()->sync($request->permission_ids);

        return $this->success(new RoleResource($role->fresh('permissions')), 'Permissions mises à jour.');
    }

    public function destroy(Role $role): JsonResponse
    {
        abort_if($role->is_system, 403, 'Ce rôle système ne peut pas être supprimé.');
        abort_if($role->users()->exists(), 409, 'Impossible de supprimer un rôle assigné à des utilisateurs.');

        $role->delete();

        return $this->success(message: 'Rôle supprimé.');
    }
}
