<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockMovementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'variant' => $this->when($this->relationLoaded('variant'), fn () => [
                'sku' => $this->variant->sku,
                'name' => $this->variant->name,
            ]),
            'type' => $this->type,
            'quantity' => $this->quantity,
            'reason' => $this->reason,
            'user' => $this->when($this->relationLoaded('user') && $this->user, fn () => [
                'name' => $this->user->name,
            ]),
            'created_at' => $this->created_at,
        ];
    }
}
