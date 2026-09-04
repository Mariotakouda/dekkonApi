<?php

namespace App\Observers;

use App\Models\Payment;
use App\Services\Notification\ActivityLoggerService;

class PaymentObserver
{
    public function __construct(
        private readonly ActivityLoggerService $logger
    ) {}

    public function updated(Payment $payment): void
    {
        if ($payment->wasChanged('status')) {
            $this->logger->log(
                'payment.status_changed',
                $payment,
                ['status' => $payment->getOriginal('status')],
                ['status' => $payment->status->value]
            );
        }
    }
}
