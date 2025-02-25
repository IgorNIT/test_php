<?php

declare(strict_types=1);

namespace App\Currency\Service;

use App\Currency\Entity\Currency;
use App\Currency\Entity\ExchangeRate;

/**
 * ExchangeRate service interface
 */
interface ExchangeRateInterface
{
    /**
     * Get the exchange rate for a currency pair
     *
     * @param Currency $from
     * @param Currency $to
     * @return ExchangeRate
     */
    public function getExchangeRate(Currency $from, Currency $to): ExchangeRate;
}
