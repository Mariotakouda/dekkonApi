<?php

namespace App\Actions\Order;

use App\Enums\OrderStatus;
use App\Events\OrderStatusChanged;
use App\Exceptions\Order\InvalidOrderStatusTransitionException;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ChangeOrderStatusAction
{
    public function execute(Order $order, OrderStatus $target, ?User $changedBy = null, ?string $comment = null): Order
    {
        return DB::transaction(function () use ($order, $target, $changedBy, $comment) {
            if (! $order->canTransitionTo($target)) {
                throw new InvalidOrderStatusTransitionException($order->status->value, $target->value);
            }

            $updates = ['status' => $target];

            if ($target === OrderStatus::CONFIRMED) {
                $updates['confirmed_at'] = now();
            } elseif ($target === OrderStatus::DELIVERED) {
                $updates['delivered_at'] = now();
            } elseif ($target === OrderStatus::CANCELLED) {
                $updates['cancelled_at'] = now();
            }

            $order->update($updates);

            $order->statusHistory()->create([
                'status' => $target,
                'changed_by' => $changedBy?->id,
                'comment' => $comment,
            ]);

            $order->refresh();

            OrderStatusChanged::dispatch($order);

            return $order;
        });
    }
}
