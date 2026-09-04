<?php

namespace App\Providers;

use App\Events\OrderPlaced;
use App\Events\OrderStatusChanged;
use App\Events\PaymentSucceeded;
use App\Events\StockLevelLow;
use App\Listeners\LogOrderActivity;
use App\Listeners\NotifyLowStock;
use App\Listeners\SendOrderStatusNotification;
use App\Listeners\SendPaymentConfirmation;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        StockLevelLow::class => [
            NotifyLowStock::class,
        ],
        OrderStatusChanged::class => [
            SendOrderStatusNotification::class,
        ],
        PaymentSucceeded::class => [
            SendPaymentConfirmation::class,
        ],
        OrderPlaced::class => [
            LogOrderActivity::class,
        ],
    ];
}
