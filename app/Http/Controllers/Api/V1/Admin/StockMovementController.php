<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\StockMovementResource;
use App\Models\StockMovement;
use App\Traits\ApiResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    use ApiResponder;

    public function index(Request $request): JsonResponse
    {
        $movements = StockMovement::with(['variant.product', 'user'])
            ->when($request->query('product_variant_id'), fn ($q, $id) => $q->where('product_variant_id', $id))
            ->when($request->query('type'), fn ($q, $type) => $q->where('type', $type))
            ->latest()
            ->paginate($request->integer('per_page', 30));

        return $this->paginated($movements, StockMovementResource::class);
    }
}
