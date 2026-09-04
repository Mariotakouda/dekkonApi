<?php

namespace App\Exceptions\Stock;

use Exception;

class StockReservationExpiredException extends Exception
{
    protected $message = 'La réservation de stock a expiré.';

    public function render($request)
    {
        return response()->json(['success' => false, 'message' => $this->message], 422);
    }
}
