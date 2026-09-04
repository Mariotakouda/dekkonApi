<?php

namespace App\Actions\Order;

class CalculateOrderTotalsAction
{
    public function execute(float $subtotal, float $discountAmount, float $deliveryFee): array
    {
        $total = max(0, $subtotal - $discountAmount) + $deliveryFee;

        return [
            'subtotal' => round($subtotal, 2),
            'discount_amount' => round($discountAmount, 2),
            'delivery_fee' => round($deliveryFee, 2),
            'total_amount' => round($total, 2),
        ];
    }
}
