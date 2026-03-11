<?php

namespace App\Helpers;

class MoneyHelper
{
    /**
     * Converte um valor float (R$) para centavos (int).
     */
    public static function toCents(float $amount): int
    {
        return (int) round($amount * 100);
    }

    /**
     * Converte centavos (int) para float (R$).
     */
    public static function fromCents(int $cents): float
    {
        return $cents / 100;
    }

    /**
     * Formata centavos para o padrão brasileiro (R$).
     */
    public static function format(int $cents): string
    {
        return 'R$ ' . number_format($cents / 100, 2, ',', '.');
    }
}
