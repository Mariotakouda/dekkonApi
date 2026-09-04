<?php

namespace App\Exceptions\Stock;

use Exception;

class InvalidStockMovementException extends Exception
{
    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
        ], 422);
    }
}
