<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'PENDING';
    case CONFIRMED = 'CONFIRMED';
    case PROCESSING = 'PROCESSING';
    case READY_FOR_DELIVERY = 'READY_FOR_DELIVERY';
    case ASSIGNED = 'ASSIGNED';
    case OUT_FOR_DELIVERY = 'OUT_FOR_DELIVERY';
    case DELIVERED = 'DELIVERED';
    case CANCELLED = 'CANCELLED';
    case RETURNED = 'RETURNED';
    case REFUNDED = 'REFUNDED';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'En attente',
            self::CONFIRMED => 'Confirmée',
            self::PROCESSING => 'En préparation',
            self::READY_FOR_DELIVERY => 'Prête pour livraison',
            self::ASSIGNED => 'Affectée à un livreur',
            self::OUT_FOR_DELIVERY => 'En cours de livraison',
            self::DELIVERED => 'Livrée',
            self::CANCELLED => 'Annulée',
            self::RETURNED => 'Retournée',
            self::REFUNDED => 'Remboursée',
        };
    }

    /**
     * Transitions de statut autorisées (utile dans ChangeOrderStatusAction).
     */
    public function allowedTransitions(): array
    {
        return match ($this) {
            self::PENDING => [self::CONFIRMED, self::CANCELLED],
            self::CONFIRMED => [self::PROCESSING, self::CANCELLED],
            self::PROCESSING => [self::READY_FOR_DELIVERY, self::CANCELLED],
            self::READY_FOR_DELIVERY => [self::ASSIGNED, self::CANCELLED],
            self::ASSIGNED => [self::OUT_FOR_DELIVERY, self::CANCELLED],
            self::OUT_FOR_DELIVERY => [self::DELIVERED, self::RETURNED],
            self::DELIVERED => [self::RETURNED, self::REFUNDED],
            self::CANCELLED, self::RETURNED, self::REFUNDED => [],
        };
    }

    public function canTransitionTo(self $target): bool
    {
        return in_array($target, $this->allowedTransitions(), true);
    }
}
