<?php

namespace App\Exceptions\Delivery;

use Exception;

class DeliveryAlreadyAssignedException extends Exception
{
    protected $message = 'Cette livraison est déjà affectée à un livreur.';

    public function render($request)
    {
        return response()->json(['success' => false, 'message' => $this->message], 422);
    }
}
