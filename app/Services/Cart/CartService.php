<?php

namespace App\Services\Cart;

use App\Enums\CartStatus;
use App\Models\Cart;
use App\Models\Customer;

class CartService
{
    public function getOrCreateActiveCart(Customer $customer): Cart
    {
        return Cart::firstOrCreate(
            ['customer_id' => $customer->id, 'status' => CartStatus::ACTIVE],
        );
    }

    public function itemCount(Customer $customer): int
    {
        return $this->getOrCreateActiveCart($customer)->items()->sum('quantity');
    }

    public function clear(Cart $cart): void
    {
        $cart->items()->delete();
    }
}
