<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Http\Resources\Client\ProductResource;
use App\Models\Product;
use App\Services\Catalog\ProductQueryService;
use App\Traits\ApiResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponder;

    public function __construct(
        private readonly ProductQueryService $productQueryService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $products = $this->productQueryService->search($request);

        return $this->paginated($products, ProductResource::class);
    }

    public function show(Product $product): JsonResponse
    {
        abort_if($product->status->value !== 'ACTIVE', 404, 'Produit indisponible.');

        $product->load(['category', 'images', 'variants.inventory']);

        return $this->success(new ProductResource($product));
    }
}
