<?php

declare(strict_types=1);

namespace App\BIN\Service;

use App\BIN\Entity\BinData;

interface BinResolverInterface
{
    /**
     * Resolve the bin
     * @param int $bin
     * @return BinData
     */
    public function resolve(int $bin): BinData;
}
