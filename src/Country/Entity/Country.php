<?php

declare(strict_types=1);

namespace App\Country\Entity;

/**
 * Country entity
 * @psalm-immutable
 */
readonly class Country
{
    /**
     * @param string $code
     * @param string $name
     */
    public function __construct(
        public string $code,
        public string $name,
    ) {
    }
}
