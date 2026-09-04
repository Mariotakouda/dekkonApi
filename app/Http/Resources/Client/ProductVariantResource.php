<?php

namespace App\Http\Resources\Client;

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
            'price' => $this->effective_price,
            'attributes' => $this->attributes,
            'is_default' => $this->is_default,
            'available_quantity' => $this->whenLoaded('inventory', fn () => $this->inventory?->available_quantity ?? 0),
            'in_stock' => $this->whenLoaded('inventory', fn () => ($this->inventory?->available_quantity ?? 0) > 0),
        ];
    }
}
