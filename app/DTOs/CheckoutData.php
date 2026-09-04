<?php

namespace App\DTOs;

class CheckoutData
{
    public function __construct(
        public readonly string $addressId,
        public readonly string $paymentMethod,
        public readonly ?string $promotionCode = null,
        public readonly ?string $notes = null,
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            addressId: $request->validated('address_id'),
            paymentMethod: $request->validated('payment_method'),
            promotionCode: $request->validated('promotion_code'),
            notes: $request->validated('notes'),
        );
    }
}
