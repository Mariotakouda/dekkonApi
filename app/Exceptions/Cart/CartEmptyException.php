<?php

namespace App\Exceptions\Cart;

use Exception;

class CartEmptyException extends Exception
{
    protected $message = 'Le panier est vide.';

    public function render($request)
    {
        return response()->json(['success' => false, 'message' => $this->message], 422);
    }
}
