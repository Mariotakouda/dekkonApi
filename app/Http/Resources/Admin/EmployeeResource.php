<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_number' => $this->employee_number,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'position' => $this->position,
            'date_hired_at' => $this->date_hired_at,
            'gender' => $this->gender,
            'status' => $this->status,
            'user' => $this->when($this->relationLoaded('user'), fn () => [
                'id' => $this->user->id,
                'email' => $this->user->email,
                'phone' => $this->user->phone,
                'role' => $this->user->relationLoaded('role') && $this->user->role ? [
                    'id' => $this->user->role->id,
                    'name' => $this->user->role->name,
                    'code' => $this->user->role->code,
                ] : null,
            ]),
            'is_driver' => $this->isDriver(),
            'created_at' => $this->created_at,
        ];
    }
}
