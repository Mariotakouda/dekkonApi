<?php

namespace App\Contracts;

use App\Models\Payment;

interface PaymentGatewayInterface
{
    /**
     * Initie une transaction côté gateway.
     *
     * - Pour Flooz / Mixx by YAS (moyens "sans redirection") : $phoneNumber
     *   est requis, le paiement est poussé directement sur le téléphone du
     *   client (prompt USSD) et la réponse ne contient pas de payment_url.
     * - Pour les moyens "avec redirection" (carte bancaire) : $phoneNumber
     *   est ignoré, la réponse contient une payment_url vers la page
     *   hébergée Fedapay.
     */
    public function initiate(Payment $payment, ?string $phoneNumber = null): array;

    /**
     * Vérifie le statut réel d'une transaction auprès du gateway.
     */
    public function verify(string $transactionReference): array;
}
