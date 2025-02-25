<?php

declare(strict_types=1);

namespace App\Transaction\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use App\Core\FileSystem\Service\DocumentReader;
use App\Transaction\Service\CommissionCalculator;
use App\BIN\Service\LookupBinlistResolver;
use App\Transaction\Processor\FileTransactionsProcessor;
use App\Core\HTTP\Service\HttpClient;
use App\Currency\Service\FastForexIO;
use App\Transaction\Repository\TransactionCommissionRepository;

class CalculateCommissionCommand extends Command
{
    protected static $defaultName = 'app:calculate-commission';
    private CommissionCalculator $commissionCalculator;


    /**
     * Configure the command
     * @return void
     */
    protected function configure(): void
    {
        $this->setName(self::$defaultName)
             ->setDescription('Calculates the commission for transactions.')
             ->setHelp('This command allows you to calculate the commission for transactions...')
             ->addArgument('inputFile', InputArgument::REQUIRED, 'The input file containing the transactions.');
    }

    /**
     * Execute the command
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $inputFile = $input->getArgument('inputFile');

        // Validate if the file exists
        if (!file_exists($inputFile)) {
            $output->writeln('Input file does not exist.');
            return Command::FAILURE;
        }

        $documentReader = new DocumentReader($inputFile);

        // A litle bit messy, I know, but we don't have a DI container)
        $commissionCalculator = new CommissionCalculator(
            new LookupBinlistResolver(new HttpClient()),
            new FastForexIO(new HttpClient()),
            new TransactionCommissionRepository()
        );

        $fileTransactionsProcessor = new FileTransactionsProcessor($documentReader, $commissionCalculator);

        $commissions = $fileTransactionsProcessor->process();

        foreach ($commissions as $commission) {
            $output->writeln(number_format($commission, 2));
        }
        return Command::SUCCESS;
    }
}
