<?php

namespace App\Services\Order;

use App\Models\Order;
use Illuminate\Support\Str;

class OrderNumberGeneratorService
{
    public function generate(): string
    {
        do {
            $number = 'DKN-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}
