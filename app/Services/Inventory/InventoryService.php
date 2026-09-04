<?php

namespace App\Services\Inventory;

use App\Models\Inventory;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;

class InventoryService
{
    public function lowStockItems(): Collection
    {
        return Inventory::lowStock()->with('variant.product')->get();
    }

    public function availableQuantity(ProductVariant $variant): int
    {
        return $variant->inventory?->available_quantity ?? 0;
    }

    public function isAvailable(ProductVariant $variant, int $quantity): bool
    {
        return $this->availableQuantity($variant) >= $quantity;
    }
}
