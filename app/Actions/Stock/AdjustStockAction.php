<?php

namespace App\Actions\Stock;

use App\Enums\StockMovementType;
use App\Exceptions\Stock\InvalidStockMovementException;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdjustStockAction
{
    /**
     * Point d'entrée unique pour tout mouvement de stock manuel (section 13, section 37).
     */
    public function execute(
        ProductVariant $variant,
        StockMovementType $type,
        int $quantity,
        ?User $user = null,
        ?string $reason = null,
    ): StockMovement {
        return DB::transaction(function () use ($variant, $type, $quantity, $user, $reason) {
            $inventory = $variant->inventory()->lockForUpdate()->first();

            if (! $inventory) {
                throw new InvalidStockMovementException('Aucun inventaire trouvé pour cette variante.');
            }

            match ($type) {
                StockMovementType::IN => $inventory->increment('quantity', $quantity),
                StockMovementType::OUT => $this->decrementQuantity($inventory, $quantity),
                StockMovementType::ADJUSTMENT => $inventory->update(['quantity' => $quantity]), // valeur absolue, pas relative
                StockMovementType::RESERVATION => $inventory->increment('reserved_quantity', $quantity),
                StockMovementType::RELEASE => $this->decrementReserved($inventory, $quantity),
            };

            return StockMovement::create([
                'product_variant_id' => $variant->id,
                'user_id' => $user?->id,
                'type' => $type,
                'quantity' => $quantity,
                'reason' => $reason,
            ]);
        });
    }

    private function decrementQuantity($inventory, int $quantity): void
    {
        if ($inventory->quantity < $quantity) {
            throw new InvalidStockMovementException('Quantité insuffisante pour une sortie de stock.');
        }

        $inventory->decrement('quantity', $quantity);
    }

    private function decrementReserved($inventory, int $quantity): void
    {
        if ($inventory->reserved_quantity < $quantity) {
            throw new InvalidStockMovementException('Quantité réservée insuffisante pour une libération.');
        }

        $inventory->decrement('reserved_quantity', $quantity);
    }
}
