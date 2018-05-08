<?php

namespace App\Store\Aggregates;

use App\Store\Contracts\Aggregate;
use App\Store\Contracts\Aggregateable;
use App\Store\Contracts\Builder;
use App\Store\Contracts\Event as EventContract;
use App\Store\Contracts\Snapshot;
use App\Store\Contracts\Stream;
use App\Store\Contracts\UUID;
use App\Store\Event;
use App\Store\Factory;
use RuntimeException;

abstract class Base implements Aggregate, Aggregateable
{
    protected $uuid;

    public function __construct(UUID $uuid)
    {
        $this->uuid = $uuid;
    }

    public function uuid(): UUID
    {
        return $this->uuid;
    }

    public function stream(): Stream
    {
        return Factory::make()->stream($this);
    }

    public function snapshot(): Snapshot
    {
        return $this->stream()->snapshot();
    }

    public function add(EventContract $event): Builder
    {
        $event = Event::make(
            get_class($this),
            $this->uuid(),
            get_class($event),
            $event->payload(),
            $event->timestamp()
        );

        return Factory::make()->add($event);
    }

    public function toAggregate(): Aggregate
    {
        return $this;
    }

    protected function eventByType($type): EventContract
    {
        $event = $this->stream()->type($type)->last();

        if ( ! $event) {
            throw new RuntimeException('Event '.implode(', ', (array) $type).' not found on aggregate.');
        }

        return $event;
    }
}
