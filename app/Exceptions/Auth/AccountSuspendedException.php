<?php

namespace App\Exceptions\Auth;

use Exception;

class AccountSuspendedException extends Exception
{
    protected $message = 'Votre compte est suspendu ou inactif.';

    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => $this->message,
        ], 403);
    }
}
