<?php

namespace App\Actions\Payment;

use App\Contracts\PaymentGatewayInterface;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class ConfirmPaymentAction
{
    public function __construct(
        private readonly PaymentGatewayInterface $gateway
    ) {}

    /**
     * Appelée depuis le webhook Fedapay : vérifie le statut réel auprès du gateway
     * (jamais confiance aveugle dans le payload reçu — section 32).
     */
    public function execute(string $transactionReference): Payment
    {
        return DB::transaction(function () use ($transactionReference) {
            $payment = Payment::where('transaction_reference', $transactionReference)
                ->lockForUpdate()
                ->firstOrFail();

            if ($payment->status === PaymentStatus::SUCCESS) {
                return $payment; // idempotence : déjà confirmé, on ne rejoue rien
            }

            $verification = $this->gateway->verify($transactionReference);

            $payment->update(['metadata' => $verification['raw']]);

            if ($verification['status'] === 'approved') {
                $payment->markAsSuccessful();

                if ($verification['status'] === 'approved') {
                    $payment->markAsSuccessful();

                    $payment->order->update(['status' => OrderStatus::CONFIRMED, 'confirmed_at' => now()]);

                    $payment->order->statusHistory()->create([
                        'status' => OrderStatus::CONFIRMED,
                        'comment' => 'Paiement confirmé via Fedapay.',
                    ]);

                    \App\Events\PaymentSucceeded::dispatch($payment);
                } else {
                    $payment->markAsFailed();
                }

                $payment->order->update(['status' => OrderStatus::CONFIRMED, 'confirmed_at' => now()]);

                $payment->order->statusHistory()->create([
                    'status' => OrderStatus::CONFIRMED,
                    'comment' => 'Paiement confirmé via Fedapay.',
                ]);
            } else {
                $payment->markAsFailed();
            }

            return $payment->fresh();
        });
    }
}
