<?php

namespace App\Store;

use App\Store\Contracts\Aggregate;
use App\Store\Contracts\Builder as Contract;
use App\Store\Contracts\Event as EventContract;
use App\Store\Contracts\Stream;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Builder implements Contract
{
    protected $events;

    public function __construct()
    {
        $this->reset();
    }

    public function events(): Collection
    {
        return $this->events;
    }

    public function add(EventContract $event): Contract
    {
        $this->events->push($event);

        return $this;
    }

    public function reset(): Contract
    {
        $this->events = collect();

        return $this;
    }

    public function save(Aggregate $aggregate): Stream
    {
        DB::beginTransaction();

        $this->events()->each(function ($event) use ($aggregate) {
            $aggregate = $event->aggregate() ?? $aggregate;
            $this->saveOnAggregate($event, $aggregate);
        });

        DB::commit();

        $this->reset();

        return $aggregate->stream();
    }

    protected function saveOnAggregate(EventContract $event, Aggregate $aggregate): EventContract
    {
        $event = Event::make(
            get_class($aggregate),
            $aggregate->uuid(),
            $event->type ?? get_class($event),
            $event->payload(),
            $event->timestamp()->seconds()
        );

        return $event->save();
    }
}
