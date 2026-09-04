<?php

namespace App\Exceptions\Stock;

use Exception;

class InsufficientStockException extends Exception
{
    public function __construct(
        private readonly int $available = 0,
        private readonly int $requested = 0,
    ) {
        parent::__construct("Stock insuffisant : {$available} disponible(s), {$requested} demandé(s).");
    }

    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'available_quantity' => $this->available,
        ], 422);
    }
}
