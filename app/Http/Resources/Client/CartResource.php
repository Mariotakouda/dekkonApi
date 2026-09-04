<?php

namespace App\Http\Resources\Client;

use App\Actions\Cart\RecalculateCartAction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $calculated = app(RecalculateCartAction::class)->execute($this->resource);

        return [
            'id' => $this->id,
            'status' => $this->status,
            'items' => $calculated['items']->map(fn ($entry) => [
                'id' => $entry['item']->id,
                'product_variant_id' => $entry['item']->product_variant_id,
                'product_name' => $entry['item']->variant->product->name,
                'variant_name' => $entry['item']->variant->name,
                'quantity' => $entry['item']->quantity,
                'unit_price' => $entry['unit_price'],
                'line_total' => $entry['line_total'],
                'available_quantity' => $entry['available_quantity'],
                'is_available' => $entry['is_available'],
            ]),
            'subtotal' => $calculated['subtotal'],
            'has_unavailable_items' => $calculated['has_unavailable_items'],
        ];
    }
}
