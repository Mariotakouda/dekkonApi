<?php

namespace App\Http\Requests\Admin;

use App\Enums\CategoryAttributeType;
use App\Models\CategoryAttribute;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'category_id' => ['required', 'uuid', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:270', 'unique:products,slug'],
            'description' => ['nullable', 'string'],
            'sku' => ['nullable', 'string', 'max:100', 'unique:products,sku'],
            'price' => ['required', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'gte:price'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'brand' => ['nullable', 'string', 'max:150'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'is_featured' => ['nullable', 'boolean'],
            'status' => ['nullable', 'string', 'in:ACTIVE,INACTIVE,OUT_OF_STOCK'],

            'variants' => ['sometimes', 'array'],
            'variants.*.sku' => ['nullable', 'string', 'max:100', 'unique:product_variants,sku'],
            'variants.*.name' => ['required', 'string', 'max:150'],
            'variants.*.price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.attributes' => ['nullable', 'array'],
            'variants.*.is_active' => ['nullable', 'boolean'],
            'variants.*.is_default' => ['nullable', 'boolean'],
            'variants.*.initial_quantity' => ['nullable', 'integer', 'min:0'],
            'variants.*.low_stock_threshold' => ['nullable', 'integer', 'min:0'],

            'images' => ['sometimes', 'array'],
            'images.*.file' => ['required', 'image', 'max:4096'],
            'images.*.alt_text' => ['nullable', 'string', 'max:255'],
            'images.*.is_primary' => ['nullable', 'boolean'],
        ];

        return array_merge($rules, $this->categoryAttributeRules());
    }

    public function messages(): array
    {
        return [
            'compare_at_price.gte' => 'Le prix comparatif doit être supérieur ou égal au prix de vente.',
            'variants.*.sku.unique' => 'Le SKU de variante :input est déjà utilisé.',
        ];
    }

    protected function categoryAttributeRules(): array
    {
        $categoryId = $this->input('category_id');

        if (! $categoryId) {
            return [];
        }

        $rules = [];

        CategoryAttribute::where('category_id', $categoryId)->get()->each(function (CategoryAttribute $attribute) use (&$rules) {
            $fieldRules = [$attribute->is_required ? 'required' : 'nullable'];

            $fieldRules[] = match ($attribute->type) {
                CategoryAttributeType::NUMBER => 'numeric',
                CategoryAttributeType::BOOLEAN => 'boolean',
                CategoryAttributeType::SELECT => Rule::in($attribute->options ?? []),
                CategoryAttributeType::MULTISELECT => 'array',
                default => 'string',
            };

            if ($attribute->type === CategoryAttributeType::MULTISELECT) {
                $rules["attributes.{$attribute->key}.*"] = [Rule::in($attribute->options ?? [])];
            }

            $rules["attributes.{$attribute->key}"] = $fieldRules;
        });

        return $rules;
    }
}
