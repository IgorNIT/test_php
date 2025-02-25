<?php

namespace Tests\Services\Transaction;

use App\Transaction\Service\CommissionCalculator;
use App\Transaction\Entity\Transaction;
use App\BIN\Entity\BinInfo;
use App\Currency\Entity\ExchangeRate;
use App\Country\Entity\Country;
use PHPUnit\Framework\TestCase;
use App\Transaction\Repository\TransactionCommissionRepository;
use App\Currency\Service\FastForexIO;
use App\BIN\Service\LookupBinlistResolver;
use App\Currency\Entity\Currency;
use App\BIN\Entity\BinData;
use App\BIN\Service\BinResolverInterface;
use App\Core\HTTP\Service\HttpClient;

/**
 * @covers \App\Transaction\Service\CommissionCalculator
 * command: vendor/bin/phpunit tests/Services/Transaction/CommissionCalculatorTest.php
 * @test
 */
class CommissionCalculatorTest extends TestCase
{

    /**
     * Test the calculate method
     * @test
     */
    public function testHandler()
    {
        $defaultCurrency = new Currency('EUR');

        foreach ($this->dataProvider() as $testName => $cases) {
            foreach ($cases as $case) {
                // Create a mock for the FastForexIO
                $fastForex = $this->createMock(FastForexIO::class , [$this->createMock(HttpClient::class)]);
                $fastForex->method('getExchangeRate')
                          ->with($case['transaction']->currency, $defaultCurrency)
                          ->willReturn($case['exchangeRate']);

                // Create a mock for the BinResolver
                $binResolver = $this->createMock(LookupBinlistResolver::class , [$this->createMock(HttpClient::class)]);
                $binResolver->method('resolve')
                            ->with($case['transaction']->bin)
                            ->willReturn($case['binData']);

                // Create a mock for the TransactionCommissionRepository
                $transactionCommissionRepository = $this->createMock(TransactionCommissionRepository::class);
                $transactionCommissionRepository->method('getCountryComission')
                                                ->with($case['binData']->country)
                                                ->willReturn($case['transactionCommission']);                                

                $commissionCalculator = new CommissionCalculator( $binResolver, $fastForex, $transactionCommissionRepository);
                $commission = $commissionCalculator->calculateCommission($case['transaction']);
           
                $this->assertEquals($case['expected'], $commission, "Failed for case: " . $testName);
            }
        }   
    }

    /**
     * Data provider for the testCalculate method
     * @return \Generator
     */
    public function dataProvider(): \Generator
    {
        yield 'Case 1: EU country' => [
            [
                'transaction'  => new Transaction(411111, 101, new Currency('EUR')),
                'binData'      => new BinData(411111, 'visa', 'debit', 'visa', 'DE1', new Country('DE', 'Germany'), new Currency('EUR')),
                'exchangeRate' => new ExchangeRate(new Currency('EUR'), new Currency('EUR'), 1),
                'transactionCommission' => 0.01,
                'expected'     => 1.01,
            ],
            [
                'transaction'  => new Transaction(411111, 500, new Currency('PLN')),
                'binData'      => new BinData(411111, 'visa', 'debit', 'visa', 'PL1', new Country('PL', 'Poland'), new Currency('PLN')),
                'exchangeRate' => new ExchangeRate(new Currency('PLN'), new Currency('EUR'), 0.24),
                'transactionCommission' => 0.01,
                'expected'     => 1.20,
            ],
        ];

        yield 'Case 2: Non-EU country' => [
            [
                'transaction'  => new Transaction(411111, 100, new Currency('EUR')),
                'binData'      => new BinData(411111, 'visa', 'debit', 'visa', 'GB1', new Country('GB', 'United Kingdom'), new Currency('EUR')),
                'exchangeRate' => new ExchangeRate(new Currency('EUR'), new Currency('EUR'), 1),
                'transactionCommission' => 0.02,
                'expected'     => 2.00,
            ],
            [
                'transaction'  => new Transaction(411111, 500, new Currency('GBP')),
                'binData'      => new BinData(411111, 'visa', 'debit', 'visa', 'GB1', new Country('GB', 'United Kingdom'), new Currency('GBP')),
                'exchangeRate' => new ExchangeRate(new Currency('GBP'), new Currency('EUR'), 0.85),
                'transactionCommission' => 0.02,
                'expected'     => 8.50,
            ],
        ];
    }
}
