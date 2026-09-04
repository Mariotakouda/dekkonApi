<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function view(User $user, Order $order): bool
    {
        // Client propriétaire OU employé avec permission
        if ($user->customer && $order->customer_id === $user->customer->id) {
            return true;
        }

        return $user->hasPermission('orders.view');
    }

    public function updateStatus(User $user, Order $order): bool
    {
        return $user->hasPermission('orders.update') || $user->hasPermission('orders.confirm');
    }

    public function cancel(User $user, Order $order): bool
    {
        if ($user->customer && $order->customer_id === $user->customer->id) {
            return true;
        }

        return $user->hasPermission('orders.cancel');
    }
}
