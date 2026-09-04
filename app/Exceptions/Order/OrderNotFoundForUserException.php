<?php

namespace App\Exceptions\Order;

use Exception;

class OrderNotFoundForUserException extends Exception
{
    protected $message = 'Commande introuvable ou accès non autorisé.';

    public function render($request)
    {
        return response()->json(['success' => false, 'message' => $this->message], 404);
    }
}
