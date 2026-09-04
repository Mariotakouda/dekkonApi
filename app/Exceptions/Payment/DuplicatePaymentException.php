<?php

namespace App\Exceptions\Payment;

use Exception;

class DuplicatePaymentException extends Exception
{
    protected $message = 'Ce paiement a déjà été enregistré.';

    public function render($request)
    {
        return response()->json(['success' => false, 'message' => $this->message], 409);
    }
}
