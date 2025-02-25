<?php

declare(strict_types=1);

namespace App\Currency\Service;

use App\Currency\Entity\Currency;
use App\Currency\Entity\ExchangeRate;
use App\Currency\Service\ExchangeRateInterface;
use App\Core\HTTP\Service\HttpClientInterface;
use App\Core\HTTP\Entity\JsonHttpResponse;

/**
 * FastForexIO API Service for getting exchange rates
 * website: https://fastforex.io/
 * documentation: https://fastforex.readme.io/reference/introduction
 * @method getExchangeRate(Currency $from, Currency $to): ExchangeRate
 */
class FastForexIO implements ExchangeRateInterface
{
    /**
     * Env key for the api key
     * @var string
     */
    public const ENV_API_KEY = 'EXCHANGE_RATE_IO_API_KEY';

    /**
     * Env key for the base url
     * @var string
     */
    public const ENV_BASE_URL = 'EXCHANGE_RATE_IO_BASE_URL';

    /**
     * Constructor
     * @param HttpClientInterface $httpClient - The http client
     */
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    /**
     * Get the exchange rate for the given currencies
     * @param Currency $from - The source currency
     * @param Currency $to - The target currency
     * @return ExchangeRate - The exchange rate
     */
    public function getExchangeRate(Currency $from, Currency $to): ExchangeRate
    {
        if ($from->code === $to->code) {
            return new ExchangeRate($from, $to, 1.0);
        }

        $exchangeRateJsonResponse = $this->getExchangeRateFromApi($from, $to);
        $exchangeRate = $this->extractExchangeRateFromJsonResponse($exchangeRateJsonResponse, $to);

        return new ExchangeRate($from, $to, $exchangeRate);
    }

    /**
     * Extract the exchange rate from the json response
     * @param JsonHttpResponse $jsonHttpResponse - The response from the api
     * @param Currency $to - The target currency
     * @return float - The exchange rate
     */
    private function extractExchangeRateFromJsonResponse(JsonHttpResponse $jsonHttpResponse, Currency $to): float
    {
        return $jsonHttpResponse->body->results[$to->code];
    }

    /**
     * Get the exchange rate from the api
     * @param Currency $from - The source currency
     * @param Currency $to - The target currency
     * @return JsonHttpResponse - The response from the api
     */
    private function getExchangeRateFromApi(Currency $from, Currency $to): JsonHttpResponse
    {
        $url = $this->getBaseUrl() . '/fetch-multi?from=' . $from->code . '&to=' . $to->code . '&api_key=' . $this->getApiKey();
        return $this->httpClient->sendRequest('GET', $url);
    }

    /**
     * Get the base url for the exchange rate api
     * @return string - The base url
     */
    private function getBaseUrl(): string
    {
        $baseUrl = $_ENV[self::ENV_BASE_URL];

        if (!$baseUrl) {
            throw new \Exception('Exchange rate io base url is not set');
        }

        return $baseUrl;
    }

    /**
     * Get the api key for the exchange rate io
     * @return string - The api key
     */
    private function getApiKey(): string
    {
        $apiKey = $_ENV[self::ENV_API_KEY];

        if (!$apiKey) {
            throw new \Exception('Exchange rate io api key is not set');
        }

        return $apiKey;
    }
}
