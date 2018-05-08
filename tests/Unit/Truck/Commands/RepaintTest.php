<?php

namespace Tests\Unit\Truck\Commands;

use App\Store\Values\UUID;
use App\Truck\Aggregates\Truck;
use App\Truck\Commands\Repaint as Command;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RepaintTest extends TestCase
{
    use DatabaseTransactions;

    public function testRun()
    {
        $aggregate = new Truck(new UUID());

        $stream = app(Command::class)
           ->truck($aggregate)
           ->color('red')();

        $this->assertInstanceOf('App\Store\Contracts\Stream', $stream);

        $events = $stream->events();

        $this->assertCount(1, $events);

        $event = $events->first();
        $payload = $event->payload;
        $this->assertSame('App\Truck\Events\Repainted', $event->type);
        $this->assertArrayHasKey('color', $payload);
        $this->assertSame('red', ((object) $payload)->color);
    }
}
