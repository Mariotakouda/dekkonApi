<?php

namespace App\Actions\Stock;

use App\Enums\StockMovementType;
use App\Exceptions\Stock\InsufficientStockException;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class DecrementStockAction
{
    /**
     * Sortie définitive de stock (ex: commande livrée) : décrémente quantity réel ET reserved_quantity.
     */
    public function execute(ProductVariant $variant, int $quantity, ?Order $order = null): void
    {
        DB::transaction(function () use ($variant, $quantity, $order) {
            $inventory = $variant->inventory()->lockForUpdate()->first();

            if (! $inventory || $inventory->quantity < $quantity) {
                throw new InsufficientStockException($inventory?->quantity ?? 0, $quantity);
            }

            $inventory->decrement('quantity', $quantity);

            if ($inventory->reserved_quantity >= $quantity) {
                $inventory->decrement('reserved_quantity', $quantity);
            }

            StockMovement::create([
                'product_variant_id' => $variant->id,
                'type' => StockMovementType::OUT,
                'quantity' => $quantity,
                'reason' => $order ? "Sortie commande {$order->order_number}" : 'Sortie de stock',
                'reference_type' => $order ? Order::class : null,
                'reference_id' => $order?->id,
            ]);
        });
    }
}
