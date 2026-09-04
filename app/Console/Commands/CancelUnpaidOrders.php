<?php

namespace App\Console\Commands;

use App\Actions\Order\CancelOrderAction;
use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Console\Command;

class CancelUnpaidOrders extends Command
{
    protected $signature = 'dekkon:cancel-unpaid-orders {--hours=24}';
    protected $description = 'Annule les commandes PENDING non payées depuis X heures';

    public function handle(CancelOrderAction $action): void
    {
        $hours = (int) $this->option('hours');

        $orders = Order::where('status', OrderStatus::PENDING)
            ->where('placed_at', '<', now()->subHours($hours))
            ->get();

        foreach ($orders as $order) {
            try {
                $action->execute($order, 'Annulée automatiquement — non payée dans les délais.');
            } catch (\Exception $e) {
                $this->error("Erreur sur commande {$order->order_number} : {$e->getMessage()}");
            }
        }

        $this->info("{$orders->count()} commande(s) annulée(s).");
    }
}
