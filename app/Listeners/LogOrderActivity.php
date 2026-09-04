<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Services\Notification\ActivityLoggerService;

class LogOrderActivity
{
    public function __construct(
        private readonly ActivityLoggerService $logger
    ) {}

    public function handle(OrderPlaced $event): void
    {
        $this->logger->log('order.placed', $event->order, null, [
            'order_number' => $event->order->order_number,
            'total_amount' => $event->order->total_amount,
        ]);
    }
}
