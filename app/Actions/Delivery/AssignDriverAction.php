<?php

namespace App\Actions\Delivery;

use App\Actions\Order\ChangeOrderStatusAction;
use App\Enums\DriverStatus;
use App\Enums\OrderStatus;
use App\Exceptions\Delivery\DriverNotAvailableException;
use App\Models\Delivery;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AssignDriverAction
{
    public function __construct(private readonly ChangeOrderStatusAction $changeOrderStatus)
    {
    }

    public function execute(Delivery $delivery, Driver $driver, ?User $changedBy = null): Delivery
    {
        return DB::transaction(function () use ($delivery, $driver, $changedBy) {
            if (! $driver->isAvailable()) {
                throw new DriverNotAvailableException();
            }

            $delivery->assignDriver($driver);
            $driver->update(['status' => DriverStatus::BUSY]);

            // Fait avancer la commande elle-même (READY_FOR_DELIVERY →
            // ASSIGNED) : sans ça, le statut de la commande n'avait jamais
            // aucun moyen de refléter qu'un livreur a été affecté.
            $order = $delivery->order;
            if ($order->status->canTransitionTo(OrderStatus::ASSIGNED)) {
                $this->changeOrderStatus->execute(
                    $order,
                    OrderStatus::ASSIGNED,
                    $changedBy,
                    "Livreur affecté : {$driver->employee?->fullName()}",
                );
            }

            return $delivery->fresh('driver');
        });
    }
}
