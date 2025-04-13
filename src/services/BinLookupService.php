<?php
namespace Services;

class BinLookupService implements BinLookupServiceInterface
{
    public function getCountryCodeFromBin(string $bin): string
    {
        $response = @file_get_contents('https://lookup.binlist.net/' . $bin);
        if (!$response) {
            throw new \Exception("Failed to fetch BIN data for BIN: $bin");
        }

        $data = json_decode($response, true);
        return $data['country']['alpha2'] ?? '';
    }
}