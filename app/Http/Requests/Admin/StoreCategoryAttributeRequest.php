<?php

namespace App\Http\Requests\Admin;

use App\Enums\CategoryAttributeType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class StoreCategoryAttributeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoryId = $this->route('category')?->id;

        return [
            'key' => [
                'required', 'string', 'max:100', 'alpha_dash',
                Rule::unique('category_attributes', 'key')->where('category_id', $categoryId),
            ],
            'label' => ['required', 'string', 'max:150'],
            'type' => ['required', new Enum(CategoryAttributeType::class)],
            'options' => ['required_if:type,select,multiselect', 'array'],
            'options.*' => ['string'],
            'is_required' => ['nullable', 'boolean'],
            'is_variant_attribute' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'key.unique' => 'Cette clé d\'attribut existe déjà pour cette catégorie.',
            'options.required_if' => 'Les options sont obligatoires pour un attribut de type choix.',
        ];
    }
}
