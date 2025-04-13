<?php

namespace App;

use Services\BinLookupServiceInterface;
use Services\ExchangeRateServiceInterface;

class CommissionCalculator
{
    private BinLookupServiceInterface $binService;
    private ExchangeRateServiceInterface $rateService;

    public function __construct(BinLookupServiceInterface $binService, ExchangeRateServiceInterface $rateService)
    {
        $this->binService = $binService;
        $this->rateService = $rateService;
    }

    public function process(string $filename): void
    {
        foreach (explode("\n", file_get_contents($filename)) as $row) {
            if (empty($row)) continue;

            $data = json_decode($row, true);
            $bin = $data['bin'];
            $amount = (float) $data['amount'];
            $currency = $data['currency'];

            $countryCode = $this->binService->getCountryCodeFromBin($bin);
            $isEu = $this->isEuCountry($countryCode);

            $rate = ($currency === 'EUR') ? 1 : $this->rateService->getExchangeRate($currency);
            $amountInEur = ($rate > 0) ? $amount / $rate : $amount;

            $commission = $amountInEur * ($isEu ? 0.01 : 0.02);
            $commission = $this->applyCeiling($commission);

            echo $commission . PHP_EOL;
        }
    }

    private function applyCeiling(float $amount): float
    {
        return ceil($amount * 100) / 100;
    }

    private function isEuCountry(string $code): bool
    {
        return in_array($code, [
            'AT','BE','BG','CY','CZ','DE','DK','EE','ES','FI','FR','GR','HR','HU',
            'IE','IT','LT','LU','LV','MT','NL','PO','PT','RO','SE','SI','SK'
        ]);
    }
}
