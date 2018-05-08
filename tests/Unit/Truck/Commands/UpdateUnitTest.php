<?php

namespace Tests\Unit\Truck\Commands;

use App\Store\Values\UUID;
use App\Truck\Aggregates\Truck;
use App\Truck\Commands\UpdateUnit as Command;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UpdateUnitTest extends TestCase
{
    use DatabaseTransactions;

    public function testRun()
    {
        $aggregate = new Truck(new UUID());

        $stream = app(Command::class)
           ->truck($aggregate)
           ->unit(2)
           ->run();

        $this->assertInstanceOf('App\Store\Contracts\Stream', $stream);

        $events = $stream->events();

        $this->assertCount(1, $events);

        $event = $events->first();
        $payload = $event->payload;
        $this->assertSame('App\Truck\Events\UnitUpdated', $event->type);
        $this->assertArrayHasKey('unit', $payload);
        $this->assertSame(2, ((object) $payload)->unit);
    }
}
