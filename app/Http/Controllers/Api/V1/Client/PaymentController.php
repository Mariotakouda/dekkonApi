<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Actions\Payment\ConfirmPaymentAction;
use App\Actions\Payment\InitiatePaymentAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\InitiatePaymentRequest;
use App\Models\Order;
use App\Traits\ApiResponder;
use FedaPay\Error\SignatureVerification;
use FedaPay\Webhook;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    use ApiResponder;

    public function initiate(InitiatePaymentRequest $request, Order $order, InitiatePaymentAction $action): JsonResponse
    {
        abort_if($order->customer_id !== $request->user()->customer->id, 403, 'Accès non autorisé.');

        $payment = $order->payments()->latest()->firstOrFail();

        $phoneNumber = $request->validated('phone_number');

        if ($payment->method->requiresPhoneNumber() && ! $phoneNumber) {
            return $this->error(
                "Le numéro de téléphone {$payment->method->label()} est requis pour ce moyen de paiement.",
                422
            );
        }

        $result = $action->execute($payment, $phoneNumber);

        return $this->success($result, 'Paiement initié.');
    }

    /**
     * Webhook Fedapay — appelé par Fedapay lui-même, pas par le mobile.
     * La signature est vérifiée via le header X-FEDAPAY-SIGNATURE avant tout
     * traitement (voir https://docs.fedapay.com/integration-api/webhooks).
     * Le statut réel est ensuite re-vérifié auprès de l'API dans
     * ConfirmPaymentAction — on ne fait jamais confiance aveuglément au
     * contenu du payload reçu, même signé.
     */
    public function callback(Request $request, ConfirmPaymentAction $action): JsonResponse
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('X-FEDAPAY-SIGNATURE');
        $secret = config('dekkon.fedapay.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\UnexpectedValueException $e) {
            Log::warning('Webhook Fedapay : payload invalide.', ['error' => $e->getMessage()]);

            return $this->error('Payload invalide.', 400);
        } catch (SignatureVerification $e) {
            Log::warning('Webhook Fedapay : signature invalide.', ['error' => $e->getMessage()]);

            return $this->error('Signature invalide.', 400);
        }

        $transactionId = $event->object_id ?? null;

        if (! $transactionId) {
            return $this->error('Référence de transaction manquante dans l\'événement.', 400);
        }

        $action->execute((string) $transactionId);

        return $this->success(message: 'Callback traité.');
    }
}
