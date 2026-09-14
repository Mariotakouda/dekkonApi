<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order' => $this->when($this->relationLoaded('order'), fn () => [
                'id' => $this->order->id,
                'order_number' => $this->order->order_number,
            ]),
            'driver' => $this->when($this->relationLoaded('driver') && $this->driver, fn () => [
                'id' => $this->driver->id,
                'name' => $this->driver->employee?->fullName() ?? '—',
                'vehicle_type' => $this->driver->vehicle_type,
                'status' => $this->driver->status,
            ]),
            'status' => $this->status,
            'delivery_fee' => $this->delivery_fee,
            'assigned_at' => $this->assigned_at,
            'picked_up_at' => $this->picked_up_at,
            'out_for_delivery_at' => $this->out_for_delivery_at,
            'delivered_at' => $this->delivered_at,
            'failed_at' => $this->failed_at,
            'failure_reason' => $this->failure_reason,
        ];
    }
}
