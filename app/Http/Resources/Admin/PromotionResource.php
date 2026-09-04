<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromotionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'type' => $this->type,
            'value' => $this->value,
            'minimum_amount' => $this->minimum_amount,
            'maximum_discount_amount' => $this->maximum_discount_amount,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'usage_limit' => $this->usage_limit,
            'usage_count' => $this->usage_count,
            'is_active' => $this->is_active,
            'is_valid' => $this->isValid(),
            'products_count' => $this->whenCounted('products'),
            'created_at' => $this->created_at,
        ];
    }
}
