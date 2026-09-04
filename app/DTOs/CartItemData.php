<?php

namespace App\DTOs;

class CartItemData
{
    public function __construct(
        public readonly string $productVariantId,
        public readonly int $quantity,
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            productVariantId: $request->validated('product_variant_id'),
            quantity: (int) $request->validated('quantity'),
        );
    }
}
