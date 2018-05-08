<?php

namespace Tests\Unit\Carrier\Values;

use App\Carrier\Values\USDOT;
use InvalidArgumentException;
use Tests\TestCase;

class USDOTTest extends TestCase
{
    public function testValidValue()
    {
        $usdot = new USDOT(1234567);
        $this->assertSame('"1234567"', json_encode($usdot));
    }

    public function testInvalidValue()
    {
        $this->expectException(InvalidArgumentException::class);

        new USDOT(12345678);
    }
}
