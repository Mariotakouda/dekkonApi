<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $employee = $this->route('employee');
        $userId = $employee?->user_id;

        return [
            'first_name' => ['sometimes', 'string', 'max:100'],
            'last_name' => ['sometimes', 'string', 'max:100'],
            'email' => ['sometimes', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'phone' => ['sometimes', 'string', 'regex:/^\+?[0-9]{8,15}$/', Rule::unique('users', 'phone')->ignore($userId)],
            'role_id' => ['sometimes', 'uuid', 'exists:roles,id'],
            'position' => ['nullable', 'string', 'max:150'],
            'status' => ['sometimes', 'string', 'in:ACTIVE,INACTIVE,SUSPENDED'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
        ];
    }
}
