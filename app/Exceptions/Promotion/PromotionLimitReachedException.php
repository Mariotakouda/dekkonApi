<?php

namespace App\Exceptions\Promotion;

use Exception;

class PromotionLimitReachedException extends Exception
{
    protected $message = 'Ce code promotionnel a atteint sa limite d\'utilisation.';

    public function render($request)
    {
        return response()->json(['success' => false, 'message' => $this->message], 422);
    }
}
