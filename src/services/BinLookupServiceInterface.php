<?php
namespace Services;

interface BinLookupServiceInterface
{
public function getCountryCodeFromBin(string $bin): string;
}
