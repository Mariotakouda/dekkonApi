<?php

namespace App\Enums;

enum ProductStatus: string
{
    case ACTIVE = 'ACTIVE';
    case INACTIVE = 'INACTIVE';
    case OUT_OF_STOCK = 'OUT_OF_STOCK';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Actif',
            self::INACTIVE => 'Inactif',
            self::OUT_OF_STOCK => 'Rupture de stock',
        };
    }
}
