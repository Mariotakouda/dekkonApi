<?php

namespace App\Jobs;

use App\Actions\Stock\ReleaseStockAction;
use App\Enums\CartStatus;
use App\Models\Cart;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ReleaseExpiredCartReservationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(ReleaseStockAction $releaseStock): void
    {
        $expirationMinutes = config('dekkon.stock_reservation_minutes');

        $expiredCarts = Cart::where('status', CartStatus::ACTIVE)
            ->where('updated_at', '<', now()->subMinutes($expirationMinutes))
            ->with('items.variant')
            ->get();

        foreach ($expiredCarts as $cart) {
            foreach ($cart->items as $item) {
                $releaseStock->execute($item->variant, $item->quantity);
            }

            $cart->update(['status' => CartStatus::ABANDONED]);
        }
    }
}
