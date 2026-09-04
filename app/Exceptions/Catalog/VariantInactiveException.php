<?php

namespace App\Exceptions\Catalog;

use Exception;

class VariantInactiveException extends Exception
{
    protected $message = 'Cette variante n\'est plus disponible.';

    public function render($request)
    {
        return response()->json(['success' => false, 'message' => $this->message], 422);
    }
}
