<?php

namespace App\Support;

use Illuminate\Support\Str;

class UuidGenerator
{
    public static function generate(): string
    {
        return (string) Str::uuid();
    }

    public static function isValid(string $value): bool
    {
        return Str::isUuid($value);
    }
}
