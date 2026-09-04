<?php

namespace App\Exceptions\Catalog;

use Exception;

class CategoryHasProductsException extends Exception
{
    protected $message = 'Impossible de supprimer une catégorie contenant des produits.';

    public function render($request)
    {
        return response()->json(['success' => false, 'message' => $this->message], 409);
    }
}
