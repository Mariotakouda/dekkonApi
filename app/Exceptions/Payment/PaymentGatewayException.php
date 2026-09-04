<?php

namespace App\Exceptions\Payment;

use Exception;

class PaymentGatewayException extends Exception
{
    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors du traitement du paiement : ' . $this->getMessage(),
        ], 502);
    }
}
