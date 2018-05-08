<?php

namespace App\Store;

use App\Store\Contracts\Aggregate;
use App\Store\Contracts\Event;
use App\Store\Contracts\Stream as Contract;
use App\Store\Events\Snapshot as SnapshotEvent;
use App\Store\Models\Event as Model;
use App\Store\Models\Snapshot;
use BadMethodCallException;
use Illuminate\Support\Collection;

class Stream implements Contract
{
    protected $events;

    public function __construct($events = [])
    {
        if ($events instanceof Contract) {
            $events = $events->events();
        }

        if ( ! $events instanceof Collection) {
            $events = collect($events);
        }

        $this->events = $events;
    }

    public function __call($method, $arguments = [])
    {
        if (method_exists($this->events(), $method)) {
            return $this->forwardToCollection($method, $arguments);
        }

        throw new BadMethodCallException('Method '.$method.'() does not exist on '.__CLASS__);
    }

    protected function forwardToCollection($method, $arguments)
    {
        $result = call_user_func_array([$this->events(), $method], $arguments);

        if ( ! $result instanceof Collection) {
            return $result;
        }

        return new self($result);
    }

    public function events(): Collection
    {
        return $this->events;
    }

    public function type($type): Contract
    {
        $types = array_merge([SnapshotEvent::class], (array) $type);

        return $this->filter(function ($event) use ($types) {
            $class = $event->toBase();

            return in_array($class->type ?? get_class($class), $types);
        });
    }

    public function first(): ?Event
    {
        $event = $this->events()->first();

        return $event ? $event->toBase() : null;
    }

    public function last(): ?Event
    {
        $event = $this->events()->last();

        return $event ? $event->toBase() : null;
    }

    public function get(Aggregate $aggregate): Contract
    {
        $snapshot = Snapshot::aggregate($aggregate)
            ->orderBy('timestamp', 'desc')
            ->first();

        if ($snapshot) {
            return $snapshot->toBase()->stream();
        }

        $events = Model::aggregate($aggregate)
            ->orderBy('timestamp', 'asc')
            ->get();

        return new static($events);
    }

    public function snapshot(): Collection
    {
        return Factory::make()->snapshot($this);
    }
}
