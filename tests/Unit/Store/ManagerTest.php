<?php

namespace Tests\Unit\Store;

use App\Carrier\Aggregates\Carrier;
use App\Carrier\Events\Created;
use App\Carrier\Values\USDOT;
use App\Store\Factory;
use App\Store\Values\UUID;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ManagerTest extends TestCase
{
    use DatabaseTransactions;

    protected function createAggregate()
    {
        $uuid = new UUID();
        $usdot = new USDOT(1234567);
        $aggregate = new Carrier($uuid, $usdot);
        $store = Factory::make();
        $store->add(new Created($uuid, $usdot));

        return $store->save($aggregate);
    }

    public function testSave()
    {
        $stream = $this->createAggregate();
        $this->assertCount(1, $stream->events());
    }

    public function testFind()
    {
        $stream = $this->createAggregate();
        $id = $stream->first()->toBase()->id();
        $event = Factory::make()->find($id);
        $this->assertSame($id, $event->id());
        $this->assertInstanceOf('App\Store\Contracts\Event', $event);
    }

    public function testSnapshot()
    {
        $stream = $this->createAggregate();
        $snapshots = Factory::make()->snapshot($stream);
        $this->assertCount(1, $snapshots);

        $snapshot = $snapshots->first();
        $this->assertInstanceOf('App\Store\Contracts\Snapshot', $snapshot);
        $this->assertInstanceOf(Carrier::class, $snapshot->aggregate());
        $this->assertSame($stream->first()->id(), $snapshot->eventId());
        $this->assertCount(1, $snapshot->stream()->events());
    }

    public function testProject()
    {
        $stream = $this->createAggregate();
        $projections = Factory::make()->project('App\Carrier\Projectors\Carriers', $stream);
        $this->assertSame(1, $projections);
    }
}
