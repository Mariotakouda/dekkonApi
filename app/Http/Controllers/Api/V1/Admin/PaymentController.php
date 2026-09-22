<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Actions\Payment\RefundPaymentAction;
use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\PaymentResource;
use App\Models\Payment;
use App\Traits\ApiResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    use ApiResponder;

    public function index(Request $request): JsonResponse
    {
        $payments = Payment::with('order')
            ->when($request->query('status'), fn($q, $s) => $q->where('status', $s))
            ->when($request->query('method'), fn($q, $m) => $q->where('method', $m))
            ->latest()
            ->paginate($request->integer('per_page', 20));

        return $this->paginated($payments, PaymentResource::class);
    }

    public function show(Payment $payment): JsonResponse
    {
        return $this->success(new PaymentResource($payment->load('order')));
    }

    public function refund(Request $request, Payment $payment, RefundPaymentAction $action): JsonResponse
    {
        $payment = $action->execute($payment, $request->input('reason'));

        return $this->success(new PaymentResource($payment), 'Paiement remboursé.');
    }
}
