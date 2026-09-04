<?php

namespace App\Services\Catalog;

use App\Models\Category;
use Illuminate\Support\Collection;

class CategoryTreeService
{
    /**
     * Retourne l'arborescence complète des catégories actives (section 8).
     */
    public function tree(): Collection
    {
        return Category::active()
            ->roots()
            ->with(['children' => fn ($q) => $q->active()->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();
    }
}
