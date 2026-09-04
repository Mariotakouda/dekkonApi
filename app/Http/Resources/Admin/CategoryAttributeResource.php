<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryAttributeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'key' => $this->key,
            'label' => $this->label,
            'type' => $this->type,
            'options' => $this->options,
            'is_required' => $this->is_required,
            'is_variant_attribute' => $this->is_variant_attribute,
            'sort_order' => $this->sort_order,
        ];
    }
}
