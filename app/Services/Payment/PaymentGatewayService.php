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

    public function initiate(Payment $payment, ?string $phoneNumber = null): array
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

            $mode = $payment->method->fedapayMode();

            // Carte bancaire (ou tout futur moyen sans mode direct) : flux
            // classique avec redirection vers la page hébergée Fedapay.
            if ($mode === null) {
                $token = $transaction->generateToken();

                return [
                    'flow' => 'redirect',
                    'transaction_reference' => (string) $transaction->id,
                    'payment_url' => $token->url,
                    'status' => 'pending',
                ];
            }

            // Flooz : flux "sans redirection" — on pousse directement la
            // demande de paiement (prompt USSD) sur le téléphone du client.
            //
            // En sandbox, Fedapay a supprimé les simulateurs propres à
            // chaque opérateur (moov_tg, togocel, etc.) au profit d'un mode
            // unique 'momo_test' : y envoyer 'moov_tg' renvoie "Opération
            // non autorisée". Seuls les numéros 64000001 et 66000001
            // simulent un succès dans ce mode ; tout autre numéro simule un
            // échec. En live, on garde bien le vrai mode de l'opérateur.
            // Réf : https://docs.fedapay.com/integration-api/fr/sending-requests-fr
            $isSandbox = config('dekkon.fedapay.environment') === 'sandbox';
            $effectiveMode = $isSandbox ? 'momo_test' : $mode;

            $tokenObject = $transaction->generateToken();

            $response = $transaction->sendNowWithToken($effectiveMode, $tokenObject->token, [
                'number' => $this->formatTogoNumber($phoneNumber),
                'country' => 'tg',
            ]);

            return [
                'flow' => 'push',
                'transaction_reference' => (string) $transaction->id,
                'payment_url' => null,
                'status' => $response->status ?? 'pending',
            ];
        } catch (\Exception $e) {
            throw new PaymentGatewayException($e->getMessage());
        }
    }

    /**
     * Nettoie un numéro togolais saisi sous différentes formes
     * (+228 90 00 00 00, 00228900000, 90 00 00 00...) pour ne garder que
     * les 8 chiffres locaux attendus par Fedapay (paramètre 'country' séparé).
     */
    private function formatTogoNumber(?string $phoneNumber): string
    {
        $digits = preg_replace('/\D/', '', (string) $phoneNumber);

        if (str_starts_with($digits, '00228')) {
            $digits = substr($digits, 5);
        } elseif (str_starts_with($digits, '228') && strlen($digits) > 8) {
            $digits = substr($digits, 3);
        }

        return $digits;
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
