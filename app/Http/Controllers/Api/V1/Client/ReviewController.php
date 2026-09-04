<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreReviewRequest;
use App\Http\Resources\Client\ReviewResource;
use App\Models\OrderItem;
use App\Traits\ApiResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    use ApiResponder;

    public function index(Request $request): JsonResponse
    {
        $reviews = $request->user()->customer->reviews()
            ->with('product')
            ->latest()
            ->paginate($request->integer('per_page', 15));

        return $this->paginated($reviews, ReviewResource::class);
    }

    public function store(StoreReviewRequest $request): JsonResponse
    {
        $this->authorize('create', [\App\Models\Review::class, $request->order_item_id]);

        $orderItem = OrderItem::findOrFail($request->order_item_id);

        abort_if($orderItem->hasBeenReviewed(), 409, 'Cette ligne de commande a déjà été évaluée.');

        $customer = $request->user()->customer;

        $review = $customer->reviews()->create([
            'product_id' => $orderItem->variant?->product_id ?? $this->resolveProductFromSnapshot($orderItem),
            'order_item_id' => $orderItem->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return $this->success(new ReviewResource($review->load('product')), 'Avis publié.', 201);
    }

    /**
     * Si la variante a été supprimée depuis, on retrouve le produit via le nom snapshotté
     * n'est pas fiable à 100% — fallback nécessaire uniquement dans ce cas limite.
     */
    private function resolveProductFromSnapshot(OrderItem $orderItem): ?string
    {
        return $orderItem->variant?->product_id;
    }
}
