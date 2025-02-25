<?php

declare(strict_types=1);

namespace App\Transaction\Processor;

use App\Core\FileSystem\Service\DocumentReader;
use App\Transaction\Entity\Transaction;
use App\Transaction\Service\CommissionCalculator;
use App\Currency\Entity\Currency;

class FileTransactionsProcessor
{
    /**
     * @param DocumentReader $documentReader
     * @param CommissionCalculator $commissionCalculator
     */
    public function __construct(
        private DocumentReader $documentReader,
        private CommissionCalculator $commissionCalculator,
    ) {
    }


    /**
     * Process the transactions
     * @return float[] - The commissions
     */
    public function process(): array
    {
        $comissions = [];

        foreach ($this->documentReader->getLines() as $line) {
            $transactionJson = (object) json_decode($line, true);

            $transaction = new Transaction(
                bin: (int) $transactionJson->bin,
                amount: (float) $transactionJson->amount,
                currency: new Currency($transactionJson->currency)
            );

            $commissions[] = $this->commissionCalculator->calculateCommission($transaction);
        }

        return $commissions;
    }
}
