<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'category_id' => ['sometimes', 'uuid', 'exists:categories,id'],
            'name' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:270', Rule::unique('products', 'slug')->ignore($productId)],
            'description' => ['nullable', 'string'],
            'sku' => ['sometimes', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($productId)],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'brand' => ['nullable', 'string', 'max:150'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'is_featured' => ['nullable', 'boolean'],
            'status' => ['sometimes', 'string', 'in:ACTIVE,INACTIVE,OUT_OF_STOCK'],
        ];
    }
}
