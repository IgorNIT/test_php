<?php

declare(strict_types=1);

namespace App\Currency\Entity;

/**
 * Currency entity
 * @param string $code - Currency code
 */
readonly class Currency
{
    public function __construct(
        public readonly string $code,
    ) {
    }
}
