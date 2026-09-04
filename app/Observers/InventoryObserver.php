<?php

namespace App\Observers;

use App\Events\StockLevelLow;
use App\Models\Inventory;

class InventoryObserver
{
    public function updated(Inventory $inventory): void
    {
        if ($inventory->wasChanged(['quantity', 'reserved_quantity']) && $inventory->isLowStock()) {
            StockLevelLow::dispatch($inventory);
        }
    }
}
