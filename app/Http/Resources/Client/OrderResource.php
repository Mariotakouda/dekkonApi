<?php

namespace App\Http\Resources\Client;

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
            'subtotal' => $this->subtotal,
            'discount_amount' => $this->discount_amount,
            'delivery_fee' => $this->delivery_fee,
            'total_amount' => $this->total_amount,
            'notes' => $this->notes,
            'placed_at' => $this->placed_at,
            'items' => $this->whenLoaded('items', fn () => $this->items->map(fn ($item) => [
                'id' => $item->id,
                'product_name' => $item->product_name,
                'sku' => $item->sku,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total_amount' => $item->total_amount,
            ])),
            'address' => $this->whenLoaded('address', fn () => [
                'recipient_name' => $this->address->recipient_name,
                'phone' => $this->address->phone,
                'city' => $this->address->city,
                'district' => $this->address->district,
                'address_line' => $this->address->address_line,
            ]),
            'payment' => $this->whenLoaded('payments', fn () => $this->payments->last() ? [
                'method' => $this->payments->last()->method,
                'status' => $this->payments->last()->status,
                'amount' => $this->payments->last()->amount,
                'phone_number' => $this->payments->last()->phone_number,
            ] : null),
            'status_history' => $this->whenLoaded('statusHistory', fn () => $this->statusHistory->map(fn ($h) => [
                'status' => $h->status,
                'comment' => $h->comment,
                'created_at' => $h->created_at,
            ])),
        ];
    }
}
