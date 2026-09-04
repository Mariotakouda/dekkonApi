<?php

namespace App\Actions\Cart;

use App\Enums\CartStatus;
use App\Exceptions\Stock\InsufficientStockException;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Customer;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

class AddItemToCartAction
{
    public function execute(Customer $customer, ProductVariant $variant, int $quantity): CartItem
    {
        return DB::transaction(function () use ($customer, $variant, $quantity) {
            $cart = Cart::firstOrCreate(
                ['customer_id' => $customer->id, 'status' => CartStatus::ACTIVE],
            );

            $existingItem = $cart->items()->where('product_variant_id', $variant->id)->first();
            $totalRequested = $quantity + ($existingItem?->quantity ?? 0);

            $inventory = $variant->inventory;
            $available = $inventory?->available_quantity ?? 0;

            if ($available < $totalRequested) {
                throw new InsufficientStockException($available, $totalRequested);
            }

            if ($existingItem) {
                $existingItem->update([
                    'quantity' => $totalRequested,
                    'unit_price' => $variant->effective_price,
                ]);

                return $existingItem->fresh();
            }

            return $cart->items()->create([
                'product_variant_id' => $variant->id,
                'quantity' => $quantity,
                'unit_price' => $variant->effective_price,
            ]);
        });
    }
}
