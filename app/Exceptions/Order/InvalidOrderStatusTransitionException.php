<?php

namespace App\Exceptions\Order;

use Exception;

class InvalidOrderStatusTransitionException extends Exception
{
    public function __construct(string $from, string $to)
    {
        parent::__construct("Transition invalide : impossible de passer de {$from} à {$to}.");
    }

    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
        ], 422);
    }
}
