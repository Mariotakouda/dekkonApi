<?php

namespace App\Actions\Payment;

use App\Enums\PaymentStatus;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class RefundPaymentAction
{
    public function execute(Payment $payment, ?string $reason = null): Payment
    {
        return DB::transaction(function () use ($payment, $reason) {
            abort_if($payment->status !== PaymentStatus::SUCCESS, 422, 'Seul un paiement réussi peut être remboursé.');

            // Remboursement Fedapay réel à implémenter selon leur API de refund si disponible en sandbox.
            $payment->update([
                'status' => PaymentStatus::REFUNDED,
                'metadata' => array_merge($payment->metadata ?? [], ['refund_reason' => $reason]),
            ]);

            return $payment->fresh();
        });
    }
}
