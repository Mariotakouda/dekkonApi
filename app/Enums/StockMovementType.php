<?php

namespace App\Enums;

enum StockMovementType: string
{
    case IN = 'IN';
    case OUT = 'OUT';
    case ADJUSTMENT = 'ADJUSTMENT';
    case RESERVATION = 'RESERVATION';
    case RELEASE = 'RELEASE';

    public function label(): string
    {
        return match ($this) {
            self::IN => 'Entrée',
            self::OUT => 'Sortie',
            self::ADJUSTMENT => 'Ajustement',
            self::RESERVATION => 'Réservation',
            self::RELEASE => 'Libération',
        };
    }

    /**
     * Indique si ce type de mouvement affecte le stock réel (quantity)
     * ou seulement le stock réservé (reserved_quantity).
     */
    public function affectsAvailableStock(): bool
    {
        return match ($this) {
            self::IN, self::OUT, self::ADJUSTMENT => true,
            self::RESERVATION, self::RELEASE => false,
        };
    }
}
