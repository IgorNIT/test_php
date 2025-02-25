<?php

declare(strict_types=1);

namespace App\Currency\Entity;

/**
 * ExchangeRate entity
 * @psalm-immutable
 */
readonly class ExchangeRate
{
    /**
     * @param Currency $from
     * @param Currency $to
     * @param float $rate
     */
    public function __construct(
        public readonly Currency $from,
        public readonly Currency $to,
        public readonly float $rate,
    ) {
    }
}
