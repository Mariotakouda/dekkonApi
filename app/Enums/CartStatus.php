<?php

namespace App\Enums;

enum CartStatus: string
{
    case ACTIVE = 'ACTIVE';
    case ABANDONED = 'ABANDONED';
    case CONVERTED = 'CONVERTED';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Actif',
            self::ABANDONED => 'Abandonné',
            self::CONVERTED => 'Converti',
        };
    }
}
