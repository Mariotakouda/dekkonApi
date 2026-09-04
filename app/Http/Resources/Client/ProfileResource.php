<?php

namespace App\Http\Resources\Client;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'customer' => $this->when($this->relationLoaded('customer') && $this->customer, [
                'first_name' => $this->customer?->first_name,
                'last_name' => $this->customer?->last_name,
                'date_of_birth' => $this->customer?->date_of_birth,
                'gender' => $this->customer?->gender,
            ]),
            'employee' => $this->when($this->relationLoaded('employee') && $this->employee, fn () => [
                'id' => $this->employee->id,
                'employee_number' => $this->employee->employee_number,
                'first_name' => $this->employee->first_name,
                'last_name' => $this->employee->last_name,
                'position' => $this->employee->position,
                'role' => $this->relationLoaded('role') && $this->role ? [
                    'code' => $this->role->code,
                    'name' => $this->role->name,
                ] : null,
                // Permissions effectives : celles du rôle + les permissions directes de l'utilisateur.
                'permissions' => $this->effectivePermissionCodes(),
            ]),
            'created_at' => $this->created_at,
        ];
    }

    /**
     * Fusionne les codes de permissions du rôle et les permissions directes
     * de l'utilisateur, sans doublons.
     *
     * @return array<int, string>
     */
    private function effectivePermissionCodes(): array
    {
        $roleCodes = $this->relationLoaded('role') && $this->role && $this->role->relationLoaded('permissions')
            ? $this->role->permissions->pluck('code')
            : collect();

        $directCodes = $this->relationLoaded('directPermissions')
            ? $this->directPermissions->pluck('code')
            : collect();

        return $roleCodes->merge($directCodes)->unique()->values()->all();
    }
}
