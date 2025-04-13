<?php

namespace Services;

interface ExchangeRateServiceInterface
{
    public function getExchangeRate(string $currency): float;
}