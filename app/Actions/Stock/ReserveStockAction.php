<?php

namespace App\Actions\Stock;

use App\Enums\StockMovementType;
use App\Exceptions\Stock\InsufficientStockException;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\StockMovement;

class ReserveStockAction
{
    public function execute(ProductVariant $variant, int $quantity, Order $order): void
    {
        $inventory = $variant->inventory()->lockForUpdate()->first();

        if (! $inventory || $inventory->available_quantity < $quantity) {
            throw new InsufficientStockException($inventory?->available_quantity ?? 0, $quantity);
        }

        $inventory->increment('reserved_quantity', $quantity);

        StockMovement::create([
            'product_variant_id' => $variant->id,
            'type' => StockMovementType::RESERVATION,
            'quantity' => $quantity,
            'reason' => "Réservation commande {$order->order_number}",
            'reference_type' => Order::class,
            'reference_id' => $order->id,
        ]);
    }
}
