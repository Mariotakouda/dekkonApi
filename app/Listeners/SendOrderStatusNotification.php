<?php

namespace App\Listeners;

use App\Enums\OrderStatus;
use App\Events\OrderStatusChanged;
use App\Services\Notification\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendOrderStatusNotification implements ShouldQueue
{
    public function __construct(
        private readonly NotificationService $notificationService
    ) {}

    public function handle(OrderStatusChanged $event): void
    {
        match ($event->order->status) {
            OrderStatus::CONFIRMED => $this->notificationService->orderConfirmed($event->order),
            OrderStatus::OUT_FOR_DELIVERY => $this->notificationService->orderShipped($event->order),
            OrderStatus::DELIVERED => $this->notificationService->orderDelivered($event->order),
            default => null,
        };
    }
}
