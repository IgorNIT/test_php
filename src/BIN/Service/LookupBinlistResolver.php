<?php

declare(strict_types=1);

namespace App\BIN\Service;

use App\Core\HTTP\Service\HttpClientInterface;
use App\BIN\Entity\BinData;
use App\BIN\Service\BinResolverInterface;
use App\Core\HTTP\Entity\JsonHttpResponse;
use App\Country\Entity\Country;
use App\Currency\Entity\Currency;


/**
 * Lookup binlist resolver
 * This resolver is used to lookup the bin data from the binlist api
 * website: https://binlist.net/
 * @method resolve(int $bin): BinData
 * 
 */
class LookupBinlistResolver implements BinResolverInterface
{
    public const ENV_LOOKUP_BINLIST_URL = 'LOOKUP_BINLIST_URL';

    /**
     * @param HttpClientInterface $httpClient
     */
    public function __construct(private HttpClientInterface $httpClient)
    {
    }

    /**
     * Resolve the bin number
     * @param int $bin
     * @return \App\BIN\Entity\BinData
     */
    public function resolve(int $bin): BinData
    {
        $data = $this->resolveApiRequest($bin);
        return $this->createBinDataEntity($bin, $data);
    }

    /**
     * Create the bin data from the api response
     * @param int $bin
     * @param \App\Core\HTTP\Entity\JsonHttpResponse $data
     * @return \App\BIN\Entity\BinData
     */
    private function createBinDataEntity(int $bin, JsonHttpResponse $data): BinData
    {
        $country = new Country($data->body->country['alpha2'], $data->body->country['name']);
        $currency = new Currency($data->body->country['currency']);

        return new BinData(
            bin: $bin,
            scheme: $data->body->scheme,
            type: $data->body->type,
            brand: $data->body->brand,
            bank_name: $data->body->bank['name'],
            country: $country,
            currency: $currency,
        );
    }

    /**
     * Resolve the api request
     * @param int $bin
     * @return \App\Core\HTTP\Entity\JsonHttpResponse
     */
    private function resolveApiRequest(int $bin): JsonHttpResponse
    {
        $url = $this->getBaseUrl().'/'.$bin;
        return $this->httpClient->sendRequest('GET', $url, ['headers' => ['Content-Type' => 'application/json']]);
    }

    /**
     * Get api base url from config file
     * @return string
     */
    private function getBaseUrl(): string
    {
        $baseUrl = $_ENV[self::ENV_LOOKUP_BINLIST_URL];

        if (!$baseUrl) {
            throw new \Exception('LOOKUP_BINLIST_URL is not set');
        }

        return $baseUrl;
    }
}
