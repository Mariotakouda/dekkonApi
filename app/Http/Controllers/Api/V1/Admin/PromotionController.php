<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePromotionRequest;
use App\Http\Resources\Admin\PromotionResource;
use App\Models\Promotion;
use App\Traits\ApiResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    use ApiResponder;

    public function index(Request $request): JsonResponse
    {
        $promotions = Promotion::withCount('products')
            ->when($request->boolean('active_only'), fn ($q) => $q->active())
            ->latest()
            ->paginate($request->integer('per_page', 20));

        return $this->paginated($promotions, PromotionResource::class);
    }

    public function store(StorePromotionRequest $request): JsonResponse
    {
        $promotion = DB::transaction(function () use ($request) {
            $promotion = Promotion::create($request->except('product_ids'));

            if ($request->filled('product_ids')) {
                $promotion->products()->sync($request->product_ids);
            }

            return $promotion;
        });

        return $this->success(new PromotionResource($promotion), 'Promotion créée.', 201);
    }

    public function show(Promotion $promotion): JsonResponse
    {
        return $this->success(new PromotionResource($promotion->loadCount('products')));
    }

    public function update(StorePromotionRequest $request, Promotion $promotion): JsonResponse
    {
        DB::transaction(function () use ($request, $promotion) {
            $promotion->update($request->except('product_ids'));

            if ($request->has('product_ids')) {
                $promotion->products()->sync($request->product_ids ?? []);
            }
        });

        return $this->success(new PromotionResource($promotion->fresh()->loadCount('products')), 'Promotion mise à jour.');
    }

    public function destroy(Promotion $promotion): JsonResponse
    {
        $promotion->delete();

        return $this->success(message: 'Promotion supprimée.');
    }
}
