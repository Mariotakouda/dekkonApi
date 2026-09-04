<?php

namespace App\Actions\Order;

use App\Enums\OrderStatus;
use App\Enums\StockMovementType;
use App\Exceptions\Order\OrderCannotBeCancelledException;
use App\Models\Order;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class CancelOrderAction
{
    public function execute(Order $order, ?string $reason = null, ?string $changedBy = null): Order
    {
        return DB::transaction(function () use ($order, $reason, $changedBy) {
            if (! $order->canBeCancelled()) {
                throw new OrderCannotBeCancelledException();
            }

            // Libérer le stock réservé pour chaque article
            foreach ($order->items as $item) {
                if (! $item->product_variant_id) {
                    continue;
                }

                $variant = $item->variant;
                $inventory = $variant?->inventory;

                if ($inventory) {
                    $inventory->decrement('reserved_quantity', $item->quantity);

                    StockMovement::create([
                        'product_variant_id' => $variant->id,
                        'type' => StockMovementType::RELEASE,
                        'quantity' => $item->quantity,
                        'reason' => "Annulation commande {$order->order_number}",
                        'reference_type' => Order::class,
                        'reference_id' => $order->id,
                    ]);
                }
            }

            $order->update([
                'status' => OrderStatus::CANCELLED,
                'cancelled_at' => now(),
            ]);

            $order->statusHistory()->create([
                'status' => OrderStatus::CANCELLED,
                'changed_by' => $changedBy,
                'comment' => $reason ?? 'Commande annulée par le client.',
            ]);

            return $order->fresh();
        });
    }
}
