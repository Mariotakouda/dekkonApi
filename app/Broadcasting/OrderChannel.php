<?php

namespace App\Broadcasting;

use App\Models\Order;
use App\Models\User;

class OrderChannel
{
    /**
     * Autorise l'utilisateur à écouter les mises à jour d'une commande précise
     * uniquement s'il en est le propriétaire (canal privé "orders.{orderId}").
     */
    public function join(User $user, Order $order): bool
    {
        return $user->customer && $order->customer_id === $user->customer->id;
    }
}
