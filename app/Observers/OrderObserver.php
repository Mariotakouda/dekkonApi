<?php

namespace App\Observers;

use App\Models\Order;
use App\Services\Notification\ActivityLoggerService;

class OrderObserver
{
    public function __construct(
        private readonly ActivityLoggerService $logger
    ) {}

    public function updated(Order $order): void
    {
        if ($order->wasChanged('status')) {
            $this->logger->log(
                'order.status_changed',
                $order,
                ['status' => $order->getOriginal('status')],
                ['status' => $order->status->value]
            );
        }
    }
}
