<?php

namespace App\Exceptions\Promotion;

use Exception;

class PromotionExpiredException extends Exception
{
    protected $message = 'Ce code promotionnel est invalide ou expiré.';

    public function render($request)
    {
        return response()->json(['success' => false, 'message' => $this->message], 422);
    }
}
