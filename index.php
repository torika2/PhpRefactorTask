<?php

require_once 'vendor/autoload.php';

use App\CommissionCalculator;
use Services\BinLookupService;
use Services\ExchangeRateService;

$filename = $argv[1] ?? null;

if (!$filename || !file_exists($filename)) {
    echo "Input file missing or not found.\n";
    exit(1);
}

$calculator = new CommissionCalculator(
    new BinLookupService(),
    new ExchangeRateService()
);

$calculator->process($filename);
