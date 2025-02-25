<?php

namespace Tests\Services\Currency;

use App\Currency\Service\FastForexIO;
use App\Currency\Entity\Currency;
use PHPUnit\Framework\TestCase;
use App\Core\HTTP\Entity\JsonHttpResponse;
use App\Core\HTTP\Service\HttpClient;
use App\Currency\Entity\ExchangeRate;

/**
 * 
 * @covers \App\Currency\Service\FastForex
 * command: vendor/bin/phpunit tests/Services/Currency/FastForexIOTest.php
 */
class FastForexIOTest extends TestCase
{
    public function test_get_exchange_rate()
    {
        // fixture for the response from fastforex.io
        $responseData =  (object) json_decode(file_get_contents(__DIR__ . '/../../fixtures/fast_forex_currency_exchange_response.json'), true);

         // Create a mock for the JsonHttpResponse
        $jsonHttpResponse = new JsonHttpResponse(200, $responseData);
 
         // Create a mock for the HttpClient
         $httpClient = $this->createMock(HttpClient::class);
         $httpClient->method('sendRequest')->willReturn($jsonHttpResponse);

        $fastForex = new FastForexIO($httpClient);

        $from = new Currency('USD');
        $to = new Currency('EUR');

        $exchangeRate = $fastForex->getExchangeRate($from, $to);

        $this->assertInstanceOf(ExchangeRate::class, $exchangeRate);
        $this->assertEquals($exchangeRate->from, $from);
        $this->assertEquals($exchangeRate->to, $to);
        $this->assertEquals($exchangeRate->rate, 0.9551);
    }
}
