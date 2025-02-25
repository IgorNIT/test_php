<?php

declare(strict_types=1);

namespace App\BIN\Entity;

use App\Currency\Entity\Currency;
use App\Country\Entity\Country;

readonly class BinData
{
    /**
     * @param int $bin - BIN number (first 6 digits of the card)
     * @param string $scheme - Scheme of the card (visa, mastercard, etc.)
     * @param string $type - Type of the card (debit, credit)
     * @param string $brand - Brand of the card (visa, mastercard, etc.)
     * @param string $bank_name - Bank name of the card
     * @param Country $country - Country of the card
     * @param Currency $currency - Currency of the card
     */
    public function __construct(
        public readonly int $bin,
        public readonly string $scheme,
        public readonly string $type,
        public readonly string $brand,
        public readonly string $bank_name,
        public readonly Country $country,
        public readonly Currency $currency
    ) {
    }
}
