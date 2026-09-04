<?php

namespace App\Exceptions\Catalog;

use Exception;

class ProductNotAvailableException extends Exception
{
    protected $message = 'Ce produit n\'est plus disponible.';

    public function render($request)
    {
        return response()->json(['success' => false, 'message' => $this->message], 404);
    }
}
