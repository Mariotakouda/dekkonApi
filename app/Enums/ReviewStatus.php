<?php

namespace App\Enums;

enum ReviewStatus: string
{
    case PENDING = 'PENDING';
    case APPROVED = 'APPROVED';
    case REJECTED = 'REJECTED';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'En attente de modération',
            self::APPROVED => 'Approuvé',
            self::REJECTED => 'Rejeté',
        };
    }
}