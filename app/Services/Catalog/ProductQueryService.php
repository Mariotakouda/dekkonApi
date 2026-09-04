<?php

namespace App\Services\Catalog;

use App\Enums\ProductStatus;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;

class ProductQueryService
{
    /**
     * Liste paginée avec recherche, filtres et tri (section 4, points 3-4).
     */
    public function search(Request $request): LengthAwarePaginator
    {
        $query = Product::query()
            ->active()
            ->with(['category', 'images', 'variants.inventory']);

        // Recherche texte libre
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', "%{$search}%")
                    ->orWhere('sku', 'ILIKE', "%{$search}%")
                    ->orWhere('brand', 'ILIKE', "%{$search}%");
            });
        }

        // Filtre catégorie
        if ($categoryId = $request->query('category_id')) {
            $query->where('category_id', $categoryId);
        }

        // Filtre prix
        if ($minPrice = $request->query('min_price')) {
            $query->where('price', '>=', $minPrice);
        }
        if ($maxPrice = $request->query('max_price')) {
            $query->where('price', '<=', $maxPrice);
        }

        // Filtre marque
        if ($brand = $request->query('brand')) {
            $query->where('brand', $brand);
        }

        // Filtre produits vedettes
        if ($request->boolean('featured')) {
            $query->featured();
        }

        // Tri
        $sort = $request->query('sort', 'newest');
        match ($sort) {
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc' => $query->orderBy('name', 'asc'),
            default => $query->orderBy('created_at', 'desc'),
        };

        return $query->paginate($request->integer('per_page', 15));
    }
}
