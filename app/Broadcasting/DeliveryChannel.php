<?php

namespace App\Broadcasting;

use App\Models\Delivery;
use App\Models\User;

class DeliveryChannel
{
    public function join(User $user, Delivery $delivery): bool
    {
        // Le client propriétaire de la commande, ou le livreur affecté
        if ($user->customer && $delivery->order->customer_id === $user->customer->id) {
            return true;
        }

        return $user->employee?->driver?->id === $delivery->driver_id;
    }
}
