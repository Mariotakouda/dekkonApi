<?php

namespace App\Enums;

enum DeliveryStatus: string
{
    case PENDING = 'PENDING';
    case ASSIGNED = 'ASSIGNED';
    case PICKED_UP = 'PICKED_UP';
    case OUT_FOR_DELIVERY = 'OUT_FOR_DELIVERY';
    case DELIVERED = 'DELIVERED';
    case FAILED = 'FAILED';
    case CANCELLED = 'CANCELLED';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'En attente',
            self::ASSIGNED => 'Affectée',
            self::PICKED_UP => 'Récupérée',
            self::OUT_FOR_DELIVERY => 'En cours de livraison',
            self::DELIVERED => 'Livrée',
            self::FAILED => 'Échouée',
            self::CANCELLED => 'Annulée',
        };
    }
}
