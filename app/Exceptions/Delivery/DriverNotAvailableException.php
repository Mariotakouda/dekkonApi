<?php

namespace App\Exceptions\Delivery;

use Exception;

class DriverNotAvailableException extends Exception
{
    protected $message = "Ce livreur n'est pas disponible actuellement.";

    public function render($request)
    {
        return response()->json(['success' => false, 'message' => $this->message], 422);
    }
}
