<?php

namespace App\Actions\Payment;

use App\Contracts\PaymentGatewayInterface;
use App\Enums\PaymentStatus;
use App\Models\Payment;

class InitiatePaymentAction
{
    public function __construct(
        private readonly PaymentGatewayInterface $gateway
    ) {}

    public function execute(Payment $payment): array
    {
        if (! $payment->requiresGateway()) {
            // CASH_ON_DELIVERY : rien à initier, le paiement se fera à la livraison
            return ['payment_url' => null, 'transaction_reference' => null];
        }

        $result = $this->gateway->initiate($payment);

        $payment->update([
            'transaction_reference' => $result['transaction_reference'],
            'provider' => 'fedapay',
            'status' => PaymentStatus::PROCESSING,
        ]);

        return $result;
    }
}
