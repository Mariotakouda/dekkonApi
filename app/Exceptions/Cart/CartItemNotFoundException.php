<?php

namespace App\Exceptions\Cart;

use Exception;

class CartItemNotFoundException extends Exception
{
    protected $message = 'Article introuvable dans le panier.';

    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => $this->message,
        ], 404);
    }
}
