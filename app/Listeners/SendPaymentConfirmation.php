<?php

namespace App\Listeners;

use App\Events\PaymentSucceeded;
use App\Services\Notification\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPaymentConfirmation implements ShouldQueue
{
    public function __construct(
        private readonly NotificationService $notificationService
    ) {}

    public function handle(PaymentSucceeded $event): void
    {
        $this->notificationService->paymentReceived($event->payment->order);
    }
}
