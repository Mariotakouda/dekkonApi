<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case CASH_ON_DELIVERY = 'CASH_ON_DELIVERY';
    case MOBILE_MONEY = 'MOBILE_MONEY';
    case CARD = 'CARD';

    public function label(): string
    {
        return match ($this) {
            self::CASH_ON_DELIVERY => 'Paiement à la livraison',
            self::MOBILE_MONEY => 'Mobile Money',
            self::CARD => 'Carte bancaire',
        };
    }

    /**
     * Indique si ce moyen de paiement passe par le gateway Fedapay
     * (par opposition au cash, géré manuellement à la livraison).
     */
    public function requiresGateway(): bool
    {
        return match ($this) {
            self::MOBILE_MONEY, self::CARD => true,
            self::CASH_ON_DELIVERY => false,
        };
    }
}
