<?php

declare(strict_types=1);

namespace App\Core\HTTP\Service;

use App\Core\HTTP\Entity\HttpResponseInterface;

interface HttpClientInterface
{
    /**
     * Send a request
     * @param string $method HTTP method
     * @param string $url URL to send the request to
     * @param array $options Request options (headers, body, etc.)
     * @return HttpResponseInterface
     */
    public function sendRequest(string $method, string $url, array $options = []): HttpResponseInterface;
}
