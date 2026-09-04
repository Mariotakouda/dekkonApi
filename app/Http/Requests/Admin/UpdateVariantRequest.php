<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $variantId = $this->route('variant')?->id;

        return [
            'sku' => ['sometimes', 'string', 'max:100', Rule::unique('product_variants', 'sku')->ignore($variantId)],
            'name' => ['sometimes', 'string', 'max:150'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'attributes' => ['nullable', 'array'],
            'is_active' => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
        ];
    }
}
