<?php

namespace App\Enums;

enum CategoryAttributeType: string
{
    case TEXT = 'text';
    case NUMBER = 'number';
    case BOOLEAN = 'boolean';
    case SELECT = 'select';
    case MULTISELECT = 'multiselect';

    public function label(): string
    {
        return match ($this) {
            self::TEXT => 'Texte',
            self::NUMBER => 'Nombre',
            self::BOOLEAN => 'Oui / Non',
            self::SELECT => 'Choix unique',
            self::MULTISELECT => 'Choix multiple',
        };
    }

    public function requiresOptions(): bool
    {
        return in_array($this, [self::SELECT, self::MULTISELECT], true);
    }
}
