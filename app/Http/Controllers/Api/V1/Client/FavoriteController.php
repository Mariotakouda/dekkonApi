<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Client\FavoriteResource;
use App\Models\Product;
use App\Traits\ApiResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    use ApiResponder;

    public function index(Request $request): JsonResponse
    {
        $favorites = $request->user()->customer->favorites()
            ->with('product.images')
            ->latest()
            ->get();

        return $this->success(FavoriteResource::collection($favorites));
    }

    public function store(Request $request, Product $product): JsonResponse
    {
        $customer = $request->user()->customer;

        $favorite = $customer->favorites()->firstOrCreate([
            'product_id' => $product->id,
        ]);

        return $this->success(new FavoriteResource($favorite->load('product')), 'Ajouté aux favoris.', 201);
    }

    public function destroy(Request $request, Product $product): JsonResponse
    {
        $request->user()->customer->favorites()
            ->where('product_id', $product->id)
            ->delete();

        return $this->success(message: 'Retiré des favoris.');
    }

    public function check(Request $request, Product $product): JsonResponse
    {
        $exists = $request->user()->customer->favorites()
            ->where('product_id', $product->id)
            ->exists();

        return $this->success(['is_favorite' => $exists]);
    }
}
