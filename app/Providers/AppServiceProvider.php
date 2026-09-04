<?php

namespace App\Providers;

use App\Contracts\PaymentGatewayInterface;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Observers\InventoryObserver;
use App\Observers\OrderObserver;
use App\Observers\PaymentObserver;
use App\Observers\ProductObserver;
use App\Services\Payment\PaymentGatewayService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(PaymentGatewayInterface::class, PaymentGatewayService::class);
    }

    public function boot(): void
    {
        Inventory::observe(InventoryObserver::class);
        Product::observe(ProductObserver::class);
        Order::observe(OrderObserver::class);
        Payment::observe(PaymentObserver::class);
    }
}
