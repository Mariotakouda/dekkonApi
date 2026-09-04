<?php

namespace App\Services\Inventory;

use App\Models\Inventory;
use Illuminate\Support\Collection;

class LowStockAlertService
{
    /**
     * Retourne tous les articles en stock faible, groupés par produit pour un rapport lisible.
     */
    public function report(): Collection
    {
        return Inventory::lowStock()
            ->with('variant.product')
            ->get()
            ->groupBy(fn ($inventory) => $inventory->variant->product->name);
    }

    public function count(): int
    {
        return Inventory::lowStock()->count();
    }
}
