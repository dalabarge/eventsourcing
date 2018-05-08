<?php

namespace Tests\Unit\Truck\Commands;

use App\Store\Values\UUID;
use App\Truck\Aggregates\Truck;
use App\Truck\Commands\ReportAccident as Command;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReportAccidentTest extends TestCase
{
    use DatabaseTransactions;

    public function testRun()
    {
        $aggregate = new Truck(new UUID());

        $stream = app(Command::class)
           ->truck($aggregate)
           ->date('2018-04-01')
           ->run();

        $this->assertInstanceOf('App\Store\Contracts\Stream', $stream);

        $events = $stream->events();

        $this->assertCount(1, $events);

        $event = $events->first();
        $payload = $event->payload;
        $this->assertSame('App\Truck\Events\AccidentReported', $event->type);
        $this->assertInstanceOf('App\Store\Contracts\Timestamp', $event->timestamp);
        $this->assertSame('2018-04-01 00:00:00', $event->timestamp->__toString());
        $this->assertArrayHasKey('accident', $payload);
        $accident = (object) ((object) $payload)->accident;
        $this->assertSame('2018-04-01', $accident->date);
    }
}
