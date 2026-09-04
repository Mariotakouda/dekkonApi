<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Actions\Order\CancelOrderAction;
use App\Actions\Order\PlaceOrderAction;
use App\DTOs\CheckoutData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\CheckoutRequest;
use App\Http\Resources\Client\OrderResource;
use App\Models\Order;
use App\Traits\ApiResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ApiResponder;

    public function index(Request $request): JsonResponse
    {
        $orders = $request->user()->customer->orders()
            ->with(['items', 'payments'])
            ->latest('placed_at')
            ->paginate($request->integer('per_page', 15));

        return $this->paginated($orders, OrderResource::class);
    }

    public function show(Request $request, Order $order): JsonResponse
    {
        abort_if($order->customer_id !== $request->user()->customer->id, 403, 'Accès non autorisé.');

        $order->load(['items', 'address', 'payments', 'statusHistory']);

        return $this->success(new OrderResource($order));
    }

    public function store(CheckoutRequest $request, PlaceOrderAction $action): JsonResponse
    {
        $customer = $request->user()->customer;
        $data = CheckoutData::fromRequest($request);

        $order = $action->execute($customer, $data);

        return $this->success(new OrderResource($order), 'Commande créée avec succès.', 201);
    }

    public function cancel(Request $request, Order $order, CancelOrderAction $action): JsonResponse
    {
        abort_if($order->customer_id !== $request->user()->customer->id, 403, 'Accès non autorisé.');

        $order = $action->execute($order, changedBy: $request->user()->id);

        return $this->success(new OrderResource($order), 'Commande annulée.');
    }
}
