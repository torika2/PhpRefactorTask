<?php

use App\CommissionCalculator;
use PHPUnit\Framework\TestCase;
use Services\BinLookupServiceInterface;
use Services\ExchangeRateServiceInterface;

class CommissionCalculatorTest extends TestCase
{
    public function testProcessCalculatesCorrectCommission()
    {
        $mockBinLookup = $this->createMock(BinLookupServiceInterface::class);
        $mockExchangeRate = $this->createMock(ExchangeRateServiceInterface::class);

        $mockBinLookup->method('getCountryCodeFromBin')->willReturn('DE'); // EU country
        $mockExchangeRate->method('getExchangeRate')->willReturn(1.25); // Fixed rate

        $calculator = new CommissionCalculator($mockBinLookup, $mockExchangeRate);

        $inputFile = __DIR__ . '/mock_input.txt';
        file_put_contents($inputFile, '{"bin":"45717360","amount":"100.00","currency":"USD"}');

        ob_start();
        $calculator->process($inputFile);
        $output = ob_get_clean();

        $this->assertEquals(0.80, (float)trim($output));

        unlink($inputFile);
    }

    public function testApplyCeiling()
    {
        $class = new \ReflectionClass(CommissionCalculator::class);
        $method = $class->getMethod('applyCeiling');
        $method->setAccessible(true);

        $calculator = new CommissionCalculator(
            $this->createMock(BinLookupServiceInterface::class),
            $this->createMock(ExchangeRateServiceInterface::class)
        );

        $this->assertEquals(0.47, $method->invoke($calculator, 0.4618));
        $this->assertEquals(1.24, $method->invoke($calculator, 1.234));
        $this->assertEquals(0.01, $method->invoke($calculator, 0.001));
    }
}
