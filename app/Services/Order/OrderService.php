<?php

namespace App\Services\Order;

use App\Models\Customer;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function totalSpent(Customer $customer): float
    {
        return (float) $customer->orders()
            ->where('status', '!=', 'CANCELLED')
            ->sum('total_amount');
    }

    public function ordersCountByStatus(): array
    {
        return Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();
    }
}
