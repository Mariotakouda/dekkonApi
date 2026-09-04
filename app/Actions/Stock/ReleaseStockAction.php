<?php

namespace App\Actions\Stock;

use App\Enums\StockMovementType;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class ReleaseStockAction
{
    /**
     * Libère une réservation sans sortir le stock réel (ex: annulation avant expédition).
     */
    public function execute(ProductVariant $variant, int $quantity, ?Order $order = null): void
    {
        DB::transaction(function () use ($variant, $quantity, $order) {
            $inventory = $variant->inventory()->lockForUpdate()->first();

            $releaseQty = min($quantity, $inventory->reserved_quantity);
            $inventory->decrement('reserved_quantity', $releaseQty);

            StockMovement::create([
                'product_variant_id' => $variant->id,
                'type' => StockMovementType::RELEASE,
                'quantity' => $releaseQty,
                'reason' => $order ? "Libération réservation commande {$order->order_number}" : 'Libération de réservation',
                'reference_type' => $order ? Order::class : null,
                'reference_id' => $order?->id,
            ]);
        });
    }
}
