<?php

namespace Tests\Unit\Carrier\Aggregates;

use App\Carrier\Commands\Create as CreateCarrier;
use App\Truck\Commands\Create as CreateTruck;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CarrierTest extends TestCase
{
    use DatabaseTransactions;

    public function testTrucks()
    {
        $carrier = app(CreateCarrier::class)->usdot(1234567)->run()->first()->aggregate();
        $truck = app(CreateTruck::class)->vin('12345678901234567')->run()->first()->aggregate();
        $carrier->addTruck($truck);

        $carrier2 = app(CreateCarrier::class)->usdot(1234567)->run()->first()->aggregate();
        $truck2 = app(CreateTruck::class)->vin('01234567123456789')->run()->first()->aggregate();
        $carrier2->addTruck($truck2);

        $trucks = $carrier->trucks();
        $this->assertCount(1, $trucks);
    }

    public function testAddTruck()
    {
        $carrier = app(CreateCarrier::class)->usdot(1234567)->run()->first()->aggregate();
        $truck = app(CreateTruck::class)->vin('12345678901234567')->run()->first()->aggregate();

        $stream = $carrier->addTruck($truck);
        $this->assertInstanceOf('App\Store\Contracts\Stream', $stream);

        $events = $stream->events();
        $this->assertCount(2, $events);
    }
}
