<?php

namespace App\Enums;

enum PromotionType: string
{
    case PERCENTAGE = 'PERCENTAGE';
    case FIXED_AMOUNT = 'FIXED_AMOUNT';

    public function label(): string
    {
        return match ($this) {
            self::PERCENTAGE => 'Pourcentage',
            self::FIXED_AMOUNT => 'Montant fixe',
        };
    }
}
