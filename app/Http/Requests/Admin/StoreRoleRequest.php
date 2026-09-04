<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:50', 'unique:roles,code', 'regex:/^[A-Z_]+$/'],
            'description' => ['nullable', 'string', 'max:255'],
            'permission_ids' => ['nullable', 'array'],
            'permission_ids.*' => ['uuid', 'exists:permissions,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.regex' => 'Le code doit être en majuscules avec underscores uniquement (ex: STOCK_MANAGER).',
        ];
    }
}
