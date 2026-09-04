<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Actions\Stock\AdjustStockAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StockMovementRequest;
use App\Http\Resources\Admin\InventoryResource;
use App\Models\Inventory;
use App\Models\ProductVariant;
use App\Traits\ApiResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    use ApiResponder;

    public function index(Request $request): JsonResponse
    {
        $inventories = Inventory::with('variant.product')
            ->when($request->boolean('low_stock'), fn ($q) => $q->lowStock())
            ->when($request->query('search'), function ($q, $search) {
                $q->whereHas('variant', fn ($sub) => $sub->where('sku', 'ILIKE', "%{$search}%"));
            })
            ->paginate($request->integer('per_page', 20));

        return $this->paginated($inventories, InventoryResource::class);
    }

    public function show(ProductVariant $variant): JsonResponse
    {
        $inventory = $variant->inventory()->firstOrFail();

        return $this->success(new InventoryResource($inventory->load('variant.product')));
    }

    public function adjust(StockMovementRequest $request, ProductVariant $variant, AdjustStockAction $action): JsonResponse
    {
        $movement = $action->execute(
            variant: $variant,
            type: \App\Enums\StockMovementType::from($request->type),
            quantity: $request->quantity,
            user: $request->user(),
            reason: $request->reason,
        );

        return $this->success([
            'movement' => $movement,
            'inventory' => new InventoryResource($variant->inventory()->first()),
        ], 'Stock ajusté.');
    }
}
