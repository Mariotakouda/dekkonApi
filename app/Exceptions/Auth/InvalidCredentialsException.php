<?php

namespace App\Exceptions\Auth;

use Exception;

class InvalidCredentialsException extends Exception
{
    protected $message = 'Identifiants incorrects.';

    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => $this->message,
        ], 401);
    }
}
