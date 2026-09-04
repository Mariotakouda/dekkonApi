<?php

namespace App\Actions\Stock;

use App\Enums\StockMovementType;
use App\Models\ProductVariant;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class IncrementStockAction
{
    /**
     * Entrée de stock (ex: réception fournisseur, section 13 : "Réception de 50 produits → IN +50").
     */
    public function execute(ProductVariant $variant, int $quantity, ?User $user = null, ?string $reason = null): void
    {
        DB::transaction(function () use ($variant, $quantity, $user, $reason) {
            $inventory = $variant->inventory()->lockForUpdate()->first();

            $inventory->increment('quantity', $quantity);

            StockMovement::create([
                'product_variant_id' => $variant->id,
                'user_id' => $user?->id,
                'type' => StockMovementType::IN,
                'quantity' => $quantity,
                'reason' => $reason ?? 'Réception de stock',
            ]);
        });
    }
}
