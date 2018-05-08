<?php

namespace Tests\Unit\Carrier\Aggregates;

use App\Carrier\Aggregates\Carrier;
use App\Carrier\Commands\Create as CreateCarrier;
use App\Truck\Commands\Create as CreateTruck;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Fluent;
use Tests\TestCase;

class TruckTest extends TestCase
{
    use DatabaseTransactions;

    public function testGetters()
    {
        $carrier = app(CreateCarrier::class)->usdot(1234567)->run()->first()->aggregate();
        $truck = app(CreateTruck::class)->vin('12345678901234567')->run()->first()->aggregate();
        $carrier->addTruck($truck);

        $this->assertSame('12345678901234567', $truck->vin()->value());
        $this->assertSame('white', $truck->color());
        $this->assertSame(1, $truck->unit());
        $this->assertInstanceOf(Fluent::class, $truck->tag());
        $this->assertInstanceOf(Carrier::class, $truck->carrier());
    }

    public function testRepaint()
    {
        $truck = app(CreateTruck::class)->vin('12345678901234567')->run()->first()->aggregate();

        $stream = $truck->repaint('red');
        $this->assertInstanceOf('App\Store\Contracts\Stream', $stream);

        $events = $stream->events();
        $this->assertCount(6, $events);
        $this->assertSame('App\Truck\Events\Repainted', $events->last()->type);
    }

    public function testReportAccident()
    {
        $truck = app(CreateTruck::class)->vin('12345678901234567')->run()->first()->aggregate();

        $stream = $truck->reportAccident('2018-04-01');
        $this->assertInstanceOf('App\Store\Contracts\Stream', $stream);

        $events = $stream->events();
        $this->assertCount(6, $events);
        $this->assertSame('App\Truck\Events\AccidentReported', $events->first()->type);
    }

    public function testUpdateUnit()
    {
        $truck = app(CreateTruck::class)->vin('12345678901234567')->run()->first()->aggregate();

        $stream = $truck->updateUnit(2);
        $this->assertInstanceOf('App\Store\Contracts\Stream', $stream);

        $events = $stream->events();
        $this->assertCount(6, $events);
        $this->assertSame('App\Truck\Events\UnitUpdated', $events->last()->type);
    }
}
