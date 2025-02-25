<?php

declare(strict_types=1);

namespace App\Core\HTTP\Entity;

readonly class JsonHttpResponse implements HttpResponseInterface
{
    /**
     * @param int $statusCode
     * @param object $body
     */
    public function __construct(
        public readonly int $statusCode,
        public readonly object $body
    ) {
    }
}
