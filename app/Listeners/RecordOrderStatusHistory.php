<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;

class RecordOrderStatusHistory
{
    public function handle(OrderStatusChanged $event): void
    {
        // Déjà géré directement dans ChangeOrderStatusAction — laissé vide intentionnellement
        // pour éviter un double enregistrement dans order_status_histories.
    }
}
