<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // protégé au niveau route via middleware permission
    }

    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'regex:/^\+?[0-9]{8,15}$/', 'unique:users,phone'],
            'password' => ['required', Password::min(8)->letters()->numbers()],
            'role_id' => ['required', 'uuid', 'exists:roles,id'],
            'position' => ['nullable', 'string', 'max:150'],
            'date_hired_at' => ['nullable', 'date'],
            'gender' => ['nullable', 'string', 'in:male,female,other'],
        ];
    }
}
