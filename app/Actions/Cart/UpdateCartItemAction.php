<?php

namespace App\Actions\Cart;

use App\Exceptions\Stock\InsufficientStockException;
use App\Models\CartItem;
use Illuminate\Support\Facades\DB;

class UpdateCartItemAction
{
    public function execute(CartItem $item, int $quantity): CartItem
    {
        return DB::transaction(function () use ($item, $quantity) {
            $inventory = $item->variant->inventory;
            $available = $inventory?->available_quantity ?? 0;

            if ($available < $quantity) {
                throw new InsufficientStockException($available, $quantity);
            }

            $item->update([
                'quantity' => $quantity,
                'unit_price' => $item->variant->effective_price,
            ]);

            return $item->fresh();
        });
    }
}
