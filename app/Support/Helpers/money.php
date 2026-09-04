<?php

use App\Support\Money;

if (! function_exists('money')) {
    function money(float $amount, string $currency = 'XOF'): Money
    {
        return Money::fromFloat($amount, $currency);
    }
}

if (! function_exists('format_fcfa')) {
    function format_fcfa(float $amount): string
    {
        return number_format($amount, 0, ',', ' ') . ' FCFA';
    }
}
