<?php

declare(strict_types=1);

namespace App\Transaction\Entity;

use App\Currency\Entity\Currency;

/**
 * Transaction entity
 * @psalm-immutable
 */
readonly class Transaction
{
    /**
     * @param int $bin - BIN number (first 6 digits of the card)
     * @param float $amount - Amount of the transaction
     * @param Currency $currency - Currency of the transaction
     */
    public function __construct(
        public int $bin,
        public float $amount,
        public Currency $currency
    ) {
    }
}
