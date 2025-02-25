<?php

namespace Tests\Services\BIN;

use App\BIN\Service\LookupBinlistResolver;
use PHPUnit\Framework\TestCase;
use App\Core\HTTP\Entity\JsonHttpResponse;
use App\Core\HTTP\Service\HttpClient;
use App\BIN\Entity\BinData;

/**
 * @covers \App\BIN\Service\LookupBinlistResolver
 */
class LookupBinlistResolverTest extends TestCase
{
    
    /**
     * Test the resolve BIN from binlist.net
     * command: vendor/bin/phpunit tests/Services/BIN/LookupBinlistResolverTest.php
     * @test
     */
    public function testResolve()
    {
        $bin = 411111;
        $responseData =  (object) json_decode(file_get_contents(__DIR__ . '/../../fixtures/lookup_binlist_net_response.json'), true);

         // Create a mock for the JsonHttpResponse
        $jsonHttpResponse = new JsonHttpResponse(200, $responseData);
 
         // Create a mock for the HttpClient
         $httpClient = $this->createMock(HttpClient::class);
         $httpClient->method('sendRequest')->willReturn($jsonHttpResponse);
 
         $resolver = new LookupBinlistResolver($httpClient);
         $binData = $resolver->resolve($bin);

         $this->assertInstanceOf(BinData::class, $binData, 'LookupBinlistResolver::resolve should return an instance of BinData');
    }
}
