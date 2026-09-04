<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreVariantRequest;
use App\Http\Requests\Admin\UpdateVariantRequest;
use App\Http\Resources\Admin\ProductVariantResource;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Traits\ApiResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ProductVariantController extends Controller
{
    use ApiResponder;

    public function index(Product $product): JsonResponse
    {
        $variants = $product->variants()->with('inventory')->get();

        return $this->success(ProductVariantResource::collection($variants));
    }

    public function store(StoreVariantRequest $request, Product $product): JsonResponse
    {
        $variant = DB::transaction(function () use ($request, $product) {
            if ($request->boolean('is_default')) {
                $product->variants()->update(['is_default' => false]);
            }

            $variant = $product->variants()->create($request->only([
                'sku', 'name', 'price', 'attributes', 'is_active', 'is_default',
            ]));

            Inventory::create([
                'product_variant_id' => $variant->id,
                'quantity' => $request->integer('initial_quantity', 0),
                'reserved_quantity' => 0,
                'low_stock_threshold' => $request->integer('low_stock_threshold', 5),
            ]);

            return $variant;
        });

        return $this->success(new ProductVariantResource($variant->load('inventory')), 'Variante créée.', 201);
    }

    public function update(UpdateVariantRequest $request, Product $product, ProductVariant $variant): JsonResponse
    {
        abort_if($variant->product_id !== $product->id, 404, 'Variante introuvable pour ce produit.');

        DB::transaction(function () use ($request, $product, $variant) {
            if ($request->boolean('is_default')) {
                $product->variants()->where('id', '!=', $variant->id)->update(['is_default' => false]);
            }

            $variant->update($request->validated());
        });

        return $this->success(new ProductVariantResource($variant->fresh('inventory')), 'Variante mise à jour.');
    }

    public function destroy(Product $product, ProductVariant $variant): JsonResponse
    {
        abort_if($variant->product_id !== $product->id, 404, 'Variante introuvable pour ce produit.');

        $hasOrderHistory = $variant->orderItems()->exists();
        abort_if($hasOrderHistory, 409, 'Impossible de supprimer une variante ayant des commandes associées. Désactivez-la plutôt.');

        $variant->delete();

        return $this->success(message: 'Variante supprimée.');
    }
}
