<?php

namespace App\DTOs;

class OrderData
{
    public function __construct(
        public readonly string $customerId,
        public readonly string $addressId,
        public readonly string $paymentMethod,
        public readonly float $subtotal,
        public readonly float $discountAmount,
        public readonly float $deliveryFee,
        public readonly float $totalAmount,
        public readonly ?string $notes = null,
    ) {}
}
