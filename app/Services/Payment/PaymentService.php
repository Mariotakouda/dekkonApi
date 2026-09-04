<?php

namespace App\Services\Payment;

use App\Enums\PaymentMethod;
use App\Models\Order;
use App\Models\Payment;

class PaymentService
{
    /**
     * Détermine si une commande doit passer par le gateway ou non,
     * selon le moyen de paiement choisi à la commande.
     */
    public function requiresGateway(Order $order): bool
    {
        $payment = $order->payments()->latest()->first();

        return $payment && PaymentMethod::from($payment->method->value)->requiresGateway();
    }

    public function latestPayment(Order $order): ?Payment
    {
        return $order->payments()->latest()->first();
    }

    public function totalPaidByCustomer(string $customerId): float
    {
        return (float) Payment::whereHas('order', fn ($q) => $q->where('customer_id', $customerId))
            ->where('status', 'SUCCESS')
            ->sum('amount');
    }
}
