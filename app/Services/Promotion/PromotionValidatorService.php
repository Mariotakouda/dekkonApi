<?php

namespace App\Services\Promotion;

use App\Models\Promotion;

class PromotionValidatorService
{
    /**
     * Validation "à blanc" d'un code promo, sans l'appliquer (utile pour prévisualiser dans le panier
     * avant le checkout réel, sans incrémenter usage_count).
     */
    public function preview(string $code, float $subtotal): array
    {
        $promotion = Promotion::where('code', $code)->first();

        if (! $promotion) {
            return ['valid' => false, 'message' => 'Code promotionnel introuvable.', 'discount' => 0];
        }

        if (! $promotion->isValid()) {
            return ['valid' => false, 'message' => 'Ce code promotionnel est expiré ou inactif.', 'discount' => 0];
        }

        if (! $promotion->meetsMinimumAmount($subtotal)) {
            return [
                'valid' => false,
                'message' => "Montant minimum de {$promotion->minimum_amount} requis.",
                'discount' => 0,
            ];
        }

        return [
            'valid' => true,
            'message' => 'Code valide.',
            'discount' => $promotion->calculateDiscount($subtotal),
        ];
    }
}
