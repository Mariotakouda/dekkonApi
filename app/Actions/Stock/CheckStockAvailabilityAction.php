<?php

namespace App\Actions\Stock;

use App\Models\ProductVariant;

class CheckStockAvailabilityAction
{
    public function execute(ProductVariant $variant, int $quantity): bool
    {
        $inventory = $variant->inventory;

        if (! $inventory) {
            return false;
        }

        return $inventory->canReserve($quantity);
    }

    public function availableQuantity(ProductVariant $variant): int
    {
        return $variant->inventory?->available_quantity ?? 0;
    }
}
