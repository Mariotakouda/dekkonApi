<?php

namespace App\Exceptions\Auth;

use Exception;

class AccountNotVerifiedException extends Exception
{
    protected $message = 'Votre compte doit être vérifié avant de continuer.';

    public function render($request)
    {
        return response()->json(['success' => false, 'message' => $this->message], 403);
    }
}
