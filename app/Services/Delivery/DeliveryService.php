<?php

namespace App\Services\Delivery;

use App\Enums\DeliveryStatus;
use App\Models\Delivery;
use App\Models\Order;

class DeliveryService
{
    /**
     * Crée l'enregistrement de livraison associé à une commande (section 23 : Order 1 --- 0..1 Delivery).
     */
    public function createForOrder(Order $order, float $deliveryFee = 0): Delivery
    {
        return Delivery::firstOrCreate(
            ['order_id' => $order->id],
            [
                'status' => DeliveryStatus::PENDING,
                'delivery_fee' => $deliveryFee,
            ]
        );
    }
}
