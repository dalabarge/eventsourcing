<?php

namespace Tests\Unit\Carrier\Commands;

use App\Carrier\Commands\AddTruck as Command;
use App\Carrier\Commands\Create as CreateCarrier;
use App\Truck\Commands\Create as CreateTruck;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AddTruckTest extends TestCase
{
    use DatabaseTransactions;

    public function testRun()
    {
        $carrier = app(CreateCarrier::class)->usdot(1234567)->run()->first()->aggregate();
        $truck = app(CreateTruck::class)->vin('12345678901234567')->run()->first()->aggregate();

        $stream = app(Command::class)
           ->usdot($carrier)
           ->vin($truck)
           ->run();

        $this->assertInstanceOf('App\Store\Contracts\Stream', $stream);

        $events = $stream->events();

        $this->assertCount(2, $events);

        $event = $events->get(1);
        $this->assertSame('App\Carrier\Events\TruckAdded', $event->type);
        $this->assertArrayHasKey('truck', $event->payload);

        $stream = $truck->stream();
        $event = $stream->events()->last();
        $this->assertSame('App\Truck\Events\USDOTAssigned', $event->type);
        $this->assertArrayHasKey('usdot', $event->payload);
    }
}
