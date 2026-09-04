<?php

namespace App\Exceptions\Auth;

use Exception;

class UnauthorizedActionException extends Exception
{
    protected $message = 'Vous n\'êtes pas autorisé à effectuer cette action.';

    public function render($request)
    {
        return response()->json(['success' => false, 'message' => $this->message], 403);
    }
}
