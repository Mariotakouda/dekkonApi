<?php

namespace App\Actions\Order;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;

class ConfirmOrderAction
{
    public function __construct(
        private readonly ChangeOrderStatusAction $changeStatus
    ) {}

    public function execute(Order $order, User $employee): Order
    {
        return $this->changeStatus->execute($order, OrderStatus::CONFIRMED, $employee, 'Commande confirmée par l\'équipe.');
    }
}
