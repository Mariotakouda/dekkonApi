<?php

namespace App\Listeners;

use App\Events\StockLevelLow;
use App\Models\Employee;
use App\Models\Notification;
use App\Models\Role;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyLowStock implements ShouldQueue
{
    public function handle(StockLevelLow $event): void
    {
        $variant = $event->inventory->variant;

        // Notifie tous les employés ayant le rôle STOCK_MANAGER ou ADMIN
        $stockManagerRole = Role::whereIn('code', ['STOCK_MANAGER', 'ADMIN'])->pluck('id');

        $employeeUserIds = Employee::whereHas('user', fn ($q) => $q->whereIn('role_id', $stockManagerRole))
            ->with('user')
            ->get()
            ->pluck('user.id');

        foreach ($employeeUserIds as $userId) {
            Notification::create([
                'user_id' => $userId,
                'type' => 'low_stock_alert',
                'title' => 'Stock faible',
                'message' => "Le stock de \"{$variant->product->name} — {$variant->name}\" est faible ({$event->inventory->available_quantity} restant(s)).",
                'data' => [
                    'product_variant_id' => $variant->id,
                    'available_quantity' => $event->inventory->available_quantity,
                ],
            ]);
        }
    }
}
