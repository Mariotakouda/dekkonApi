<?php

namespace App\Actions\Order;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;

class CreateOrderSnapshotAction
{
    /**
     * Fige les données produit et adresse au moment de la commande (section 20).
     */
    public function execute(Order $order, Cart $cart, Address $address): void
    {
        foreach ($cart->items as $item) {
            $variant = $item->variant;
            $unitPrice = $variant->effective_price;

            $order->items()->create([
                'product_variant_id' => $variant->id,
                'product_name' => $variant->product->name,
                'sku' => $variant->sku,
                'quantity' => $item->quantity,
                'unit_price' => $unitPrice,
                'discount_amount' => 0,
                'total_amount' => $unitPrice * $item->quantity,
            ]);
        }

        $order->address()->create([
            'recipient_name' => $address->recipient_name,
            'phone' => $address->phone,
            'city' => $address->city,
            'district' => $address->district,
            'address_line' => $address->address_line,
            'landmark_note' => $address->landmark_note,
            'latitude' => $address->latitude,
            'longitude' => $address->longitude,
        ]);
    }
}
