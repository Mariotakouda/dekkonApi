<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'PENDING';
    case PROCESSING = 'PROCESSING';
    case SUCCESS = 'SUCCESS';
    case FAILED = 'FAILED';
    case CANCELLED = 'CANCELLED';
    case REFUNDED = 'REFUNDED';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'En attente',
            self::PROCESSING => 'En cours de traitement',
            self::SUCCESS => 'Réussi',
            self::FAILED => 'Échoué',
            self::CANCELLED => 'Annulé',
            self::REFUNDED => 'Remboursé',
        };
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::SUCCESS, self::FAILED, self::CANCELLED, self::REFUNDED], true);
    }
}
