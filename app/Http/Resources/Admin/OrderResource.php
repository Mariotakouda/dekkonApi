<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status,
            'customer' => $this->when($this->relationLoaded('customer'), fn () => [
                'id' => $this->customer->id,
                'name' => $this->customer->fullName(),
                'phone' => $this->customer->user->phone ?? null,
            ]),
            'subtotal' => $this->subtotal,
            'discount_amount' => $this->discount_amount,
            'delivery_fee' => $this->delivery_fee,
            'total_amount' => $this->total_amount,
            'notes' => $this->notes,
            'placed_at' => $this->placed_at,
            'confirmed_at' => $this->confirmed_at,
            'delivered_at' => $this->delivered_at,
            'cancelled_at' => $this->cancelled_at,
            'items' => $this->whenLoaded('items', fn () => $this->items->map(fn ($item) => [
                'id' => $item->id,
                'product_name' => $item->product_name,
                'sku' => $item->sku,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total_amount' => $item->total_amount,
            ])),
            'address' => $this->whenLoaded('address'),
            'payments' => $this->whenLoaded('payments', fn () => $this->payments->map(fn ($p) => [
                'id' => $p->id,
                'method' => $p->method,
                'status' => $p->status,
                'amount' => $p->amount,
                'transaction_reference' => $p->transaction_reference,
            ])),
            'delivery' => $this->whenLoaded('delivery', fn () => $this->delivery ? [
                'status' => $this->delivery->status,
                'driver_id' => $this->delivery->driver_id,
            ] : null),
            'status_history' => $this->whenLoaded('statusHistory', fn () => $this->statusHistory->map(fn ($h) => [
                'status' => $h->status,
                'comment' => $h->comment,
                'changed_by' => $h->changedBy?->name,
                'created_at' => $h->created_at,
            ])),
        ];
    }
}
