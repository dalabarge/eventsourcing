<?php

namespace Tests\Unit\Carrier\Commands;

use App\Carrier\Commands\Create as Command;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use DatabaseTransactions;

    public function testRun()
    {
        $stream = app(Command::class)
           ->usdot('1234567')
           ->run();

        $this->assertInstanceOf('App\Store\Contracts\Stream', $stream);

        $events = $stream->events();

        $this->assertCount(1, $events);

        $event = $events->get(0);
        $this->assertSame('App\Carrier\Events\Created', $event->type);
        $this->assertArrayHasKey('uuid', $event->payload);
        $this->assertArrayHasKey('usdot', $event->payload);
        $this->assertSame('1234567', ((object) $event->payload)->usdot);
    }
}
