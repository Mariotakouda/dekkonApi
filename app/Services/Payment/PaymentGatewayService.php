<?php

namespace App\Services\Payment;

use App\Contracts\PaymentGatewayInterface;
use App\Exceptions\Payment\PaymentGatewayException;
use App\Models\Payment;
use FedaPay\FedaPay;
use FedaPay\Transaction;

class PaymentGatewayService implements PaymentGatewayInterface
{
    public function __construct()
    {
        FedaPay::setApiKey(config('dekkon.fedapay.secret_key'));
        FedaPay::setEnvironment(config('dekkon.fedapay.environment'));
    }

    public function initiate(Payment $payment): array
    {
        try {
            $order = $payment->order()->with('customer.user')->first();
            $user = $order->customer->user;

            $transaction = Transaction::create([
                'description' => "Commande {$order->order_number} — DEKKON",
                'amount' => (int) $payment->amount, // Fedapay attend un entier (FCFA, pas de centimes)
                'currency' => ['iso' => config('dekkon.currency')],
                'callback_url' => config('app.url') . '/api/v1/payments/callback',
                'customer' => [
                    'firstname' => $order->customer->first_name,
                    'lastname' => $order->customer->last_name,
                    'email' => $user->email,
                    'phone_number' => [
                        'number' => $user->phone,
                        'country' => 'TG',
                    ],
                ],
            ]);

            $token = $transaction->generateToken();

            return [
                'transaction_reference' => (string) $transaction->id,
                'payment_url' => $token->url,
            ];
        } catch (\Exception $e) {
            throw new PaymentGatewayException($e->getMessage());
        }
    }

    public function verify(string $transactionReference): array
    {
        try {
            $transaction = Transaction::retrieve($transactionReference);

            return [
                'status' => $transaction->status, // 'pending', 'approved', 'declined', 'canceled'
                'amount' => $transaction->amount,
                'raw' => $transaction->toArray(),
            ];
        } catch (\Exception $e) {
            throw new PaymentGatewayException($e->getMessage());
        }
    }
}
