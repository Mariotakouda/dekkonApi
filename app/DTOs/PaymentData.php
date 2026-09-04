<?php

namespace App\DTOs;

class PaymentData
{
    public function __construct(
        public readonly string $orderId,
        public readonly string $method,
        public readonly float $amount,
        public readonly ?string $transactionReference = null,
        public readonly ?string $provider = null,
    ) {}
}
