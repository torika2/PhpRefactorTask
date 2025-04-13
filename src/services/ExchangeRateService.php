<?php

namespace Services;

class ExchangeRateService implements ExchangeRateServiceInterface
{
    public function getExchangeRate(string $currency): float
    {
        $response = @file_get_contents('https://api.exchangeratesapi.io/latest');
        if (!$response) {
            throw new \Exception("Failed to fetch exchange rate data");
        }

        $data = json_decode($response, true);
        return $data['rates'][$currency] ?? 0;
    }
}