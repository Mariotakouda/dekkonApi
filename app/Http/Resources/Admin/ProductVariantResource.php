<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'name' => $this->name,
            'price' => $this->price,
            'effective_price' => $this->effective_price,
            'attributes' => $this->attributes,
            'is_active' => $this->is_active,
            'is_default' => $this->is_default,
            'inventory' => $this->whenLoaded('inventory', fn () => $this->inventory ? [
                'quantity' => $this->inventory->quantity,
                'reserved_quantity' => $this->inventory->reserved_quantity,
                'available_quantity' => $this->inventory->available_quantity,
                'low_stock_threshold' => $this->inventory->low_stock_threshold,
                'is_low_stock' => $this->inventory->isLowStock(),
            ] : null),
        ];
    }
}
