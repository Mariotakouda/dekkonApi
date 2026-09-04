<?php

namespace App\Services\Notification;

use App\Models\Notification;
use App\Models\Order;
use App\Models\User;

class NotificationService
{
    public function notify(User $user, string $type, string $title, string $message, ?array $data = null): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    public function orderConfirmed(Order $order): void
    {
        $this->notify(
            $order->customer->user,
            'order_confirmed',
            'Commande confirmée',
            "Votre commande {$order->order_number} a été confirmée.",
            ['order_id' => $order->id]
        );
    }

    public function paymentReceived(Order $order): void
    {
        $this->notify(
            $order->customer->user,
            'payment_received',
            'Paiement reçu',
            "Le paiement de votre commande {$order->order_number} a été confirmé.",
            ['order_id' => $order->id]
        );
    }

    public function orderShipped(Order $order): void
    {
        $this->notify(
            $order->customer->user,
            'order_shipped',
            'Commande expédiée',
            "Votre commande {$order->order_number} est en cours de livraison.",
            ['order_id' => $order->id]
        );
    }

    public function orderDelivered(Order $order): void
    {
        $this->notify(
            $order->customer->user,
            'order_delivered',
            'Commande livrée',
            "Votre commande {$order->order_number} a été livrée. Merci pour votre confiance !",
            ['order_id' => $order->id]
        );
    }
}
