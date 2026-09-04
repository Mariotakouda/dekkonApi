<?php

namespace App\Exceptions\Cart;

use Exception;

class MultipleActiveCartException extends Exception
{
    protected $message = 'Un client ne peut avoir qu\'un seul panier actif.';

    public function render($request)
    {
        return response()->json(['success' => false, 'message' => $this->message], 409);
    }
}
