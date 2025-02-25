<?php

declare(strict_types=1);

namespace App\Transaction\Service;

use App\Currency\Service\ExchangeRateInterface;
use App\Transaction\Entity\Transaction;
use App\BIN\Service\BinResolverInterface;
use App\Transaction\Repository\TransactionCommissionRepository;
use App\Currency\Entity\Currency;
use App\Country\Entity\Country;

/**
 * Commission calculator
 * This service is used to calculate the commission for a transaction
 * @method calculateCommission(Transaction $transaction): float
 */
class CommissionCalculator
{
    /**
     * Default currency code key (EUR, USD, etc.)
     */
    public const ENV_DEFAULT_CURRENCY_CODE = 'DEFAULT_CURRENCY_CODE';


    /**
     * @param BinResolverInterface $binResolver
     * @param ExchangeRateInterface $exchangeRate
     * @param TransactionCommissionRepository $transactionCommissionRepository
     */
    public function __construct(
        private BinResolverInterface $binResolverService,
        private ExchangeRateInterface $exchangeRateService,
        private TransactionCommissionRepository $transactionCommissionRepository
    ) {
    }

    /**
     * Calculate the commission
     * @param App\Transaction\Entity\Transaction $transaction
     * @return float
     */
    public function calculateCommission(Transaction $transaction): float
    {
        // Resolve the bin info
        $binInfo = $this->binResolverService->resolve($transaction->bin);

        // Get the exchange rate
        $totalAmountInDefaultCurrency = $this->totalAmountInDefaultCurrency($transaction);

        // Get the commission rate
        $commissionRate = $this->getCommissionRate($binInfo->country);

        // Calculate the commission
        return $this->calculate($totalAmountInDefaultCurrency, $commissionRate);
    }

    /**
     * Calculate the commission
     * @param float $total
     * @param float $commissionRate
     * @return float
     */
    private function calculate(float $total, float $commissionRate): float
    {
        $result = $total * $commissionRate;
        return round($result, 2);
    }

    /**
     * Get the total amount in default currency
     * @param Transaction $transaction
     * @return float
     */
    private function totalAmountInDefaultCurrency(Transaction $transaction): float
    {
        $defaultCurrency = $this->getDefaultCurrency();
        $exchangeRate = $this->exchangeRateService->getExchangeRate($transaction->currency, $defaultCurrency);

        return $transaction->amount * $exchangeRate->rate;
    }

    /**
     * Get the default currency
     * @return Currency
     */
    private function getDefaultCurrency(): Currency
    {
        $defaultCurrencyCode = $_ENV[self::ENV_DEFAULT_CURRENCY_CODE];

        if (!$defaultCurrencyCode) {
            throw new \Exception('Default currency code is not set');
        }

        return new Currency($defaultCurrencyCode);
    }

    /**
     * Get the commission rate
     * @return float
     */
    private function getCommissionRate(Country $country): float
    {
        return $this->transactionCommissionRepository->getCountryComission($country);
    }
}
