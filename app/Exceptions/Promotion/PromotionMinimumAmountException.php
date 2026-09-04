<?php

namespace App\Exceptions\Promotion;

use Exception;

class PromotionMinimumAmountException extends Exception
{
    public function __construct(private readonly float $minimumAmount)
    {
        parent::__construct("Montant minimum de {$minimumAmount} requis pour ce code promotionnel.");
    }

    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
        ], 422);
    }
}
