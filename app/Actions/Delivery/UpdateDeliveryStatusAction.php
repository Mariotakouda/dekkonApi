<?php

namespace App\Actions\Delivery;

use App\Actions\Order\ChangeOrderStatusAction;
use App\Enums\DeliveryStatus;
use App\Enums\DriverStatus;
use App\Enums\OrderStatus;
use App\Models\Delivery;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateDeliveryStatusAction
{
    public function __construct(private readonly ChangeOrderStatusAction $changeOrderStatus)
    {
    }

    public function execute(Delivery $delivery, DeliveryStatus $status, ?string $failureReason = null, ?User $changedBy = null): Delivery
    {
        return DB::transaction(function () use ($delivery, $status, $failureReason, $changedBy) {
            match ($status) {
                DeliveryStatus::PICKED_UP => $delivery->markAsPickedUp(),
                DeliveryStatus::OUT_FOR_DELIVERY => $this->handleOutForDelivery($delivery, $changedBy),
                DeliveryStatus::DELIVERED => $this->handleDelivered($delivery, $changedBy),
                DeliveryStatus::FAILED => $delivery->markAsFailed($failureReason ?? 'Non précisé'),
                default => $delivery->update(['status' => $status]),
            };

            // Libère le livreur une fois la livraison terminée (succès ou échec)
            if (in_array($status, [DeliveryStatus::DELIVERED, DeliveryStatus::FAILED, DeliveryStatus::CANCELLED], true)) {
                $delivery->driver?->update(['status' => DriverStatus::AVAILABLE]);
            }

            return $delivery->fresh('driver');
        });
    }

    private function handleOutForDelivery(Delivery $delivery, ?User $changedBy): void
    {
        $delivery->markAsOutForDelivery();

        $order = $delivery->order;
        if ($order->status->canTransitionTo(OrderStatus::OUT_FOR_DELIVERY)) {
            $this->changeOrderStatus->execute(
                $order,
                OrderStatus::OUT_FOR_DELIVERY,
                $changedBy,
                'Le livreur a récupéré la commande.',
            );
        }
    }

    private function handleDelivered(Delivery $delivery, ?User $changedBy): void
    {
        $delivery->markAsDelivered();

        $order = $delivery->order;
        if ($order->status->canTransitionTo(OrderStatus::DELIVERED)) {
            $this->changeOrderStatus->execute(
                $order,
                OrderStatus::DELIVERED,
                $changedBy,
                'Livraison confirmée.',
            );
        }
    }
}
