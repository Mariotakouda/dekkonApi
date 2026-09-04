<?php

namespace App\Exceptions\Payment;

use Exception;

class PaymentAlreadyProcessedException extends Exception
{
    protected $message = 'Ce paiement a déjà été traité.';

    public function render($request)
    {
        return response()->json(['success' => false, 'message' => $this->message], 409);
    }
}
