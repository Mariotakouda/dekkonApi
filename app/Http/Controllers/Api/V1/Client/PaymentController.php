<?php

namespace App\Http\Controllers\Api\V1\Client;

use App\Actions\Payment\ConfirmPaymentAction;
use App\Actions\Payment\InitiatePaymentAction;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Traits\ApiResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use ApiResponder;

    public function initiate(Request $request, Order $order, InitiatePaymentAction $action): JsonResponse
    {
        abort_if($order->customer_id !== $request->user()->customer->id, 403, 'Accès non autorisé.');

        $payment = $order->payments()->latest()->firstOrFail();

        $result = $action->execute($payment);

        return $this->success($result, 'Paiement initié.');
    }

    /**
     * Webhook Fedapay — appelé par Fedapay lui-même, pas par le mobile.
     * Route publique mais protégée par vérification de signature (à ajouter selon doc Fedapay).
     */
    public function callback(Request $request, ConfirmPaymentAction $action): JsonResponse
    {
        $reference = $request->input('id') ?? $request->input('transaction_reference');

        if (! $reference) {
            return $this->error('Référence de transaction manquante.', 400);
        }

        $action->execute((string) $reference);

        return $this->success(message: 'Callback traité.');
    }
}
