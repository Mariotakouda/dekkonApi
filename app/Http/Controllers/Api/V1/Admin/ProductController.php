<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Actions\Catalog\CreateProductWithVariantsAction;
use App\DTOs\ProductCreationData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Http\Resources\Admin\ProductResource;
use App\Models\Product;
use App\Traits\ApiResponder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponder, AuthorizesRequests;

    public function index(Request $request): JsonResponse
    {
        $products = Product::with(['category', 'images'])
            ->when($request->query('category_id'), fn ($q, $id) => $q->where('category_id', $id))
            ->when($request->query('status'), fn ($q, $status) => $q->where('status', $status))
            ->when($request->query('search'), function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'ILIKE', "%{$search}%")
                        ->orWhere('sku', 'ILIKE', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return $this->paginated($products, ProductResource::class);
    }

    public function store(StoreProductRequest $request, CreateProductWithVariantsAction $action): JsonResponse
    {
        $this->authorize('create', Product::class);

        $product = $action->execute(ProductCreationData::fromRequest($request));

        return $this->success(new ProductResource($product), 'Produit créé.', 201);
    }

    public function show(Product $product): JsonResponse
    {
        $product->load(['category', 'images', 'variants.inventory', 'attributeValues.categoryAttribute']);

        return $this->success(new ProductResource($product));
    }

    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $this->authorize('update', $product);

        $product->update($request->validated());

        return $this->success(new ProductResource($product->fresh(['category', 'images', 'variants'])), 'Produit mis à jour.');
    }

    public function destroy(Product $product): JsonResponse
    {
        $this->authorize('delete', $product);

        $hasOrderHistory = $product->variants()->whereHas('orderItems')->exists();

        abort_if($hasOrderHistory, 409, 'Impossible de supprimer un produit ayant des commandes associées. Désactivez-le plutôt.');

        $product->delete();

        return $this->success(message: 'Produit supprimé.');
    }
}
