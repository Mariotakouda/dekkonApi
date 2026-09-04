<?php

namespace App\Console\Commands;

use App\Events\StockLevelLow;
use App\Models\Inventory;
use Illuminate\Console\Command;

class CheckLowStockLevels extends Command
{
    protected $signature = 'dekkon:check-low-stock';
    protected $description = 'Vérifie et notifie les niveaux de stock faibles';

    public function handle(): void
    {
        $lowStockItems = Inventory::lowStock()->get();

        foreach ($lowStockItems as $inventory) {
            StockLevelLow::dispatch($inventory);
        }

        $this->info("{$lowStockItems->count()} article(s) en stock faible signalé(s).");
    }
}
