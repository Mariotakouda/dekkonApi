<?php

namespace App\Actions\Cart;

use App\Models\Cart;

class RecalculateCartAction
{
    /**
     * Recalcule le panier à partir des prix réels des variantes (section 14).
     * Retourne un tableau enrichi, ne modifie rien en base — c'est un calcul à la volée.
     */
    public function execute(Cart $cart): array
    {
        $cart->load('items.variant.inventory', 'items.variant.product');

        $items = $cart->items->map(function ($item) {
            $variant = $item->variant;
            $unitPrice = $variant->effective_price;
            $available = $variant->inventory?->available_quantity ?? 0;

            return [
                'item' => $item,
                'unit_price' => $unitPrice,
                'line_total' => $unitPrice * $item->quantity,
                'available_quantity' => $available,
                'is_available' => $available >= $item->quantity,
            ];
        });

        return [
            'items' => $items,
            'subtotal' => $items->sum('line_total'),
            'has_unavailable_items' => $items->contains(fn ($i) => ! $i['is_available']),
        ];
    }
}
