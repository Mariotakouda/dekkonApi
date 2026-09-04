<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Actions\Order\CancelOrderAction;
use App\Actions\Order\ChangeOrderStatusAction;
use App\Actions\Order\ConfirmOrderAction;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Http\Resources\Admin\OrderResource;
use App\Models\Order;
use App\Traits\ApiResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ApiResponder;

    public function index(Request $request): JsonResponse
    {
        $orders = Order::with(['customer.user', 'payments'])
            ->when($request->query('status'), fn ($q, $status) => $q->where('status', $status))
            ->when($request->query('search'), function ($q, $search) {
                $q->where('order_number', 'ILIKE', "%{$search}%");
            })
            ->when($request->query('from'), fn ($q, $from) => $q->whereDate('placed_at', '>=', $from))
            ->when($request->query('to'), fn ($q, $to) => $q->whereDate('placed_at', '<=', $to))
            ->latest('placed_at')
            ->paginate($request->integer('per_page', 20));

        return $this->paginated($orders, OrderResource::class);
    }

    public function show(Order $order): JsonResponse
    {
        $this->authorize('view', $order);

        $order->load(['customer.user', 'items', 'address', 'payments', 'delivery', 'statusHistory.changedBy']);

        return $this->success(new OrderResource($order));
    }

    public function confirm(Request $request, Order $order, ConfirmOrderAction $action): JsonResponse
    {
        $this->authorize('updateStatus', $order);

        $order = $action->execute($order, $request->user());

        return $this->success(new OrderResource($order), 'Commande confirmée.');
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order, ChangeOrderStatusAction $action): JsonResponse
    {
        $this->authorize('updateStatus', $order);

        $order = $action->execute(
            $order,
            OrderStatus::from($request->status),
            $request->user(),
            $request->comment,
        );

        return $this->success(new OrderResource($order), 'Statut de la commande mis à jour.');
    }

    public function cancel(Request $request, Order $order, CancelOrderAction $action): JsonResponse
    {
        $this->authorize('cancel', $order);

        $order = $action->execute($order, $request->input('reason'), $request->user()->id);

        return $this->success(new OrderResource($order), 'Commande annulée.');
    }
}
