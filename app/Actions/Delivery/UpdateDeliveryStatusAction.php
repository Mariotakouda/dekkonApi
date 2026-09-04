<?php

namespace App\Actions\Delivery;

use App\Enums\DeliveryStatus;
use App\Enums\DriverStatus;
use App\Enums\OrderStatus;
use App\Models\Delivery;
use Illuminate\Support\Facades\DB;

class UpdateDeliveryStatusAction
{
    public function execute(Delivery $delivery, DeliveryStatus $status, ?string $failureReason = null): Delivery
    {
        return DB::transaction(function () use ($delivery, $status, $failureReason) {
            match ($status) {
                DeliveryStatus::PICKED_UP => $delivery->markAsPickedUp(),
                DeliveryStatus::OUT_FOR_DELIVERY => $delivery->markAsOutForDelivery(),
                DeliveryStatus::DELIVERED => $this->handleDelivered($delivery),
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

    private function handleDelivered(Delivery $delivery): void
    {
        $delivery->markAsDelivered();

        $order = $delivery->order;
        $order->update(['status' => OrderStatus::DELIVERED, 'delivered_at' => now()]);

        $order->statusHistory()->create([
            'status' => OrderStatus::DELIVERED,
            'comment' => 'Livraison confirmée.',
        ]);
    }
}