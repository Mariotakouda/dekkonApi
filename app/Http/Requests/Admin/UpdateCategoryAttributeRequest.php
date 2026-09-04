<?php

namespace App\Http\Requests\Admin;

use App\Enums\CategoryAttributeType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class UpdateCategoryAttributeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')?->id;
        $attributeId = $this->route('attribute')?->id;

        return [
            'key' => [
                'sometimes', 'string', 'max:100', 'alpha_dash',
                Rule::unique('category_attributes', 'key')->where('category_id', $categoryId)->ignore($attributeId),
            ],
            'label' => ['sometimes', 'string', 'max:150'],
            'type' => ['sometimes', new Enum(CategoryAttributeType::class)],
            'options' => ['nullable', 'array'],
            'options.*' => ['string'],
            'is_required' => ['nullable', 'boolean'],
            'is_variant_attribute' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
