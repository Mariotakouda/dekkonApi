<?php

namespace App\Contracts;

use App\Models\Payment;

interface PaymentGatewayInterface
{
    /**
     * Initie une transaction côté gateway, retourne l'URL de paiement + référence.
     */
    public function initiate(Payment $payment): array;

    /**
     * Vérifie le statut réel d'une transaction auprès du gateway.
     */
    public function verify(string $transactionReference): array;
}
