<?php

declare(strict_types=1);

namespace App\Core\HTTP\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Response;
use App\Core\HTTP\Entity\JsonHttpResponse;
use App\Core\HTTP\Entity\HttpResponseInterface;
use RuntimeException;

class HttpClient implements HttpClientInterface
{
    /**
     * Default headers for the request
     */
    public const DEFAULT_HEADER = [
        'User-Agent'      => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:109.0) Gecko/20100101 Firefox/109.0',
        'Accept-Language' => 'en-US,en;q=0.5',
        'Accept-Encoding' => 'gzip, deflate, br',
        'Accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
        'Referer'         => 'http://www.google.com/',
    ];

    /**
     * @var Client
     */
    private Client $client;


    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Send a request
     * @param string $method
     * @param string $url
     * @param array $options
     * @return HttpResponseInterface
     */
    public function sendRequest(string $method, string $url, array $options = []): HttpResponseInterface
    {

        $options = array_merge(self::DEFAULT_HEADER, $options);

        try {
            $response = $this->client->request($method, $url, $options);

            if ($response->getStatusCode() !== 200) {
                throw new RuntimeException($response->getBody()->getContents(), $response->getStatusCode());
            }

            return $this->createResponse($response);
        } catch (RequestException $e) {
            throw new RuntimeException($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Create a response
     * @param GuzzleHttp\Psr7\Response $response
     * @return HttpResponseInterface
     */
    private function createResponse(Response $response): HttpResponseInterface
    {
        if ($this->isJson($response)) {
            return new JsonHttpResponse(
                statusCode: $response->getStatusCode(),
                body: $this->getBody($response)
            );
        }

        throw new RuntimeException('Invalid response format');
    }

    /**
     * Check if the response is json
     * @param GuzzleHttp\Psr7\Response $response
     * @return bool
     */
    private function isJson(Response $response): bool
    {
        return str_starts_with($response->getHeader('Content-Type')[0], 'application/json');
    }

    /**
     * Get the body of the response
     * @param GuzzleHttp\Psr7\Response $response
     * @return string|object
     */
    private function getBody(Response $response): string|object
    {
        $body = $response->getBody()->getContents();
        return $this->isJson($response) ? (object) json_decode($body, true) : $body;
    }
}
