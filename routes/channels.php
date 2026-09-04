<?php

use App\Broadcasting\DeliveryChannel;
use App\Broadcasting\OrderChannel;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('orders.{order}', [OrderChannel::class, 'join']);
Broadcast::channel('deliveries.{delivery}', [DeliveryChannel::class, 'join']);
