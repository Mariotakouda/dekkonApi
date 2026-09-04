<?php

namespace App\Enums;

enum DriverStatus: string
{
    case AVAILABLE = 'AVAILABLE';
    case BUSY = 'BUSY';
    case INACTIVE = 'INACTIVE';

    public function label(): string
    {
        return match ($this) {
            self::AVAILABLE => 'Disponible',
            self::BUSY => 'Occupé',
            self::INACTIVE => 'Inactif',
        };
    }
}
