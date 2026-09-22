<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order' => $this->when($this->relationLoaded('order'), fn () => [
                'id' => $this->order->id,
                'order_number' => $this->order->order_number,
            ]),
            'method' => $this->method,
            'status' => $this->status,
            'amount' => $this->amount,
            'provider' => $this->provider,
            'phone_number' => $this->phone_number,
            'transaction_reference' => $this->transaction_reference,
            'paid_at' => $this->paid_at,
            'created_at' => $this->created_at,
        ];
    }
}
