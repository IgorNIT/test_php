<?php

declare(strict_types=1);

namespace App\Transaction\Repository;

use App\Transaction\Entity\Transaction;
use App\Country\Entity\Country;
use App\Transaction\Repository\AbstractRepository;

class TransactionCommissionRepository extends AbstractRepository
{
    /**
     * Get the comission  for a country
     *
     * @param Country $country
     * @return float
     */
    public function getCountryComission(Country $country): float
    {
        // Just for testing purposes while we don't have a real database
        $euCountries = ['DE', 'FR', 'GB', 'IT', 'ES', 'NL', 'PL', 'RO', 'SE', 'CZ', 'BE', 'BG', 'DK', 'EE', 'FI', 'GR', 'HU', 'IE', 'IS', 'LT', 'LU', 'LV', 'MC', 'MK', 'MT', 'NO', 'PT', 'RO', 'RS', 'SI', 'SK', 'TR', 'GB', 'US'];

        if (in_array($country->code, $euCountries)) {
            return 0.01;
        }

        return 0.02;
    }
}
