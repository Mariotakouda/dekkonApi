<?php

namespace App\Actions\Order;

use App\Exceptions\Promotion\PromotionExpiredException;
use App\Exceptions\Promotion\PromotionMinimumAmountException;
use App\Models\Order;
use App\Models\Promotion;

class ApplyPromotionToOrderAction
{
    /**
     * @return float Montant de la remise appliquée (0 si pas de promotion)
     */
    public function execute(Order $order, ?string $code, float $subtotal): float
    {
        if (! $code) {
            return 0.0;
        }

        $promotion = Promotion::where('code', $code)->first();

        if (! $promotion || ! $promotion->isValid()) {
            throw new PromotionExpiredException();
        }

        if (! $promotion->meetsMinimumAmount($subtotal)) {
            throw new PromotionMinimumAmountException((float) $promotion->minimum_amount);
        }

        $discount = $promotion->calculateDiscount($subtotal);

        $order->promotions()->create([
            'promotion_id' => $promotion->id,
            'discount_amount' => $discount,
        ]);

        $promotion->incrementUsage();

        return $discount;
    }
}
