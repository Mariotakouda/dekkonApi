<?php

namespace App\Exceptions\Order;

use Exception;

class OrderCannotBeCancelledException extends Exception
{
    protected $message = 'Cette commande ne peut plus être annulée à ce stade.';

    public function render($request)
    {
        return response()->json(['success' => false, 'message' => $this->message], 422);
    }
}
