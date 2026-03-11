<?php

namespace Tests\Unit;

use App\Helpers\MoneyHelper;
use PHPUnit\Framework\TestCase;

class MoneyHelperTest extends TestCase
{
    /**
     * Testa se a conversão para centavos está correta.
     */
    public function test_can_convert_reais_to_cents()
    {
        $this->assertEquals(1050, MoneyHelper::toCents(10.50));
        $this->assertEquals(100, MoneyHelper::toCents(1.00));
        $this->assertEquals(999, MoneyHelper::toCents(9.99));
        $this->assertEquals(0, MoneyHelper::toCents(0.00));
    }

    /**
     * Testa se a conversão de centavos para reais está correta.
     */
    public function test_can_convert_cents_to_reais()
    {
        $this->assertEquals(10.50, MoneyHelper::fromCents(1050));
        $this->assertEquals(1.00, MoneyHelper::fromCents(100));
        $this->assertEquals(9.99, MoneyHelper::fromCents(999));
        $this->assertEquals(0.00, MoneyHelper::fromCents(0));
    }

    /**
     * Testa a formatação para o padrão brasileiro de moeda.
     */
    public function test_can_format_cents_to_real_currency()
    {
        $this->assertEquals('R$ 10,50', MoneyHelper::format(1050));
        $this->assertEquals('R$ 1.000,00', MoneyHelper::format(100000));
        $this->assertEquals('R$ 0,05', MoneyHelper::format(5));
    }
}
