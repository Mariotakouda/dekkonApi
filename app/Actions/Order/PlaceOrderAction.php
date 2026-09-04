<?php

namespace App\Actions\Order;

use App\Actions\Cart\RecalculateCartAction;
use App\Actions\Stock\ReserveStockAction;
use App\DTOs\CheckoutData;
use App\Enums\CartStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Exceptions\Cart\CartEmptyException;
use App\Exceptions\Stock\InsufficientStockException;
use App\Models\Address;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Services\Order\OrderNumberGeneratorService;
use Illuminate\Support\Facades\DB;

class PlaceOrderAction
{
    public function __construct(
    private readonly RecalculateCartAction $recalculateCart,
    private readonly ReserveStockAction $reserveStock,
    private readonly CalculateOrderTotalsAction $calculateTotals,
    private readonly ApplyPromotionToOrderAction $applyPromotion,
    private readonly CreateOrderSnapshotAction $createSnapshot,
    private readonly OrderNumberGeneratorService $orderNumberGenerator,
    private readonly \App\Services\Delivery\DeliveryService $deliveryService,
) {}

    public function execute(Customer $customer, CheckoutData $data): Order
    {
        return DB::transaction(function () use ($customer, $data) {
            $cart = $customer->activeCart()->with('items.variant.inventory', 'items.variant.product')->first();

            if (! $cart || $cart->items->isEmpty()) {
                throw new CartEmptyException();
            }

            $address = Address::where('customer_id', $customer->id)->findOrFail($data->addressId);

            // 1. Vérification + calcul du panier (section 17)
            $calculated = $this->recalculateCart->execute($cart);

            if ($calculated['has_unavailable_items']) {
                throw new InsufficientStockException();
            }

            $subtotal = $calculated['subtotal'];

            // 2. Créer la commande en statut PENDING (totaux provisoires, ajustés ensuite)
            $order = Order::create([
                'customer_id' => $customer->id,
                'order_number' => $this->orderNumberGenerator->generate(),
                'status' => OrderStatus::PENDING,
                'subtotal' => $subtotal,
                'discount_amount' => 0,
                'delivery_fee' => config('dekkon.default_delivery_fee'),
                'total_amount' => $subtotal,
                'notes' => $data->notes,
                'placed_at' => now(),
            ]);

            // 3. Réserver le stock de chaque ligne (section 13)
            foreach ($cart->items as $item) {
                $this->reserveStock->execute($item->variant, $item->quantity, $order);
            }

            // 4. Appliquer la promotion éventuelle
            $discount = $this->applyPromotion->execute($order, $data->promotionCode, $subtotal);

            // 5. Recalcul des totaux définitifs
            $totals = $this->calculateTotals->execute($subtotal, $discount, (float) $order->delivery_fee);
            $order->update($totals);

            // 6. Snapshot des articles + adresse (section 20)
            $this->createSnapshot->execute($order, $cart, $address);

            // 7. Historique initial de statut
            $order->statusHistory()->create([
                'status' => OrderStatus::PENDING,
                'changed_by' => $customer->user_id,
                'comment' => 'Commande créée.',
            ]);

            // 8. Créer l'enregistrement de paiement en attente
            Payment::create([
                'order_id' => $order->id,
                'method' => $data->paymentMethod,
                'status' => PaymentStatus::PENDING,
                'amount' => $totals['total_amount'],
            ]);

            $this->deliveryService->createForOrder($order, (float) $order->delivery_fee);

            // 9. Marquer le panier comme converti
            $cart->update(['status' => CartStatus::CONVERTED]);

            return $order->fresh(['items', 'address', 'payments', 'statusHistory']);
        });
    }
}
