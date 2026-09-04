<?php

namespace App\Actions\Payment;

use App\Models\Payment;

class FailPaymentAction
{
    public function execute(Payment $payment): Payment
    {
        $payment->markAsFailed();

        return $payment->fresh();
    }
}
