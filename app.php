#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;
use App\Transaction\Command\CalculateCommissionCommand;
use Dotenv\Dotenv;

// Load environment variables from the .env file
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();


$application = new Application();

$command = new CalculateCommissionCommand();

$application->add($command);

$application->setDefaultCommand($command->getName(), true);
$application->run();