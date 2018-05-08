<?php

namespace Tests\Unit\Truck\Commands;

use App\Truck\Commands\Create as Command;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use DatabaseTransactions;

    public function testRun()
    {
        $stream = app(Command::class)
           ->vin('12345678901234567')
           ->number('LPN 123')
           ->expires('04/2018')
           ->color('white')
           ->region('TX')
           ->unit(1)
           ->run();

        $this->assertInstanceOf('App\Store\Contracts\Stream', $stream);

        $events = $stream->events();

        $this->assertCount(5, $events);

        $event = $events->get(0);
        $this->assertSame('App\Truck\Events\Created', $event->type);
        $this->assertArrayHasKey('uuid', $event->payload);

        $event = $events->get(1);
        $payload = $event->payload;
        $this->assertSame('App\Truck\Events\VINAssigned', $event->type);
        $this->assertArrayHasKey('vin', $payload);
        $this->assertSame('12345678901234567', ((object) $payload)->vin);

        $event = $events->get(2);
        $payload = $event->payload;
        $this->assertSame('App\Truck\Events\UnitUpdated', $event->type);
        $this->assertArrayHasKey('unit', $payload);
        $this->assertSame(1, ((object) $payload)->unit);

        $event = $events->get(3);
        $payload = $event->payload;
        $this->assertSame('App\Truck\Events\ColorChanged', $event->type);
        $this->assertArrayHasKey('color', $payload);
        $this->assertSame('white', ((object) $payload)->color);

        $event = $events->get(4);
        $payload = $event->payload;
        $this->assertSame('App\Truck\Events\TagRegistered', $event->type);
        $this->assertArrayHasKey('tag', $payload);
        $tag = (object) ((object) $payload)->tag;
        $this->assertSame('LPN 123', $tag->number);
        $this->assertSame('2018-04-30', $tag->expires);
        $this->assertSame('US-TX', $tag->region);
    }

    public function testTagNumberIsGenerated()
    {
        $stream = app(Command::class)
           ->vin('12345678901234567')
           ->run();

        $events = $stream->events();

        $event = $events->get(4);
        $payload = $event->payload;
        $this->assertSame('App\Truck\Events\TagRegistered', $event->type);
        $this->assertArrayHasKey('tag', $payload);
        $tag = (object) ((object) $payload)->tag;
        $this->assertSame(7, strlen($tag->number));
    }
}
