<?php

namespace App\Store\Events;

use App\Store\Contracts\Aggregate;
use App\Store\Contracts\Event as Contract;
use App\Store\Contracts\Timestamp as TimestampContract;
use App\Store\Event;
use App\Store\Factory;
use App\Store\Values\Timestamp;
use App\Store\Values\UUID;

abstract class Base implements Contract
{
    protected $id;
    protected $aggregate;
    protected $timestamp;
    protected $payload = [];

    public function __construct(array $payload = [], $timestamp = null, Aggregate $aggregate = null, int $id = null)
    {
        $this->payload = $payload;
        $this->timestamp = new Timestamp($timestamp);
        $this->aggregate = $aggregate;
        $this->id = $id;
    }

    public static function hydrate(array $payload = [], $timestamp = null, Aggregate $aggregate = null, int $id = null)
    {
        return new static($payload, $timestamp, $aggregate, $id);
    }

    public static function find($id): Contract
    {
        return Factory::make()->find($id);
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function aggregate(): ?Aggregate
    {
        return $this->aggregate;
    }

    public function payload(): array
    {
        return $this->payload;
    }

    public function getPayload(string $attribute, $default = null)
    {
        return array_get($this->payload(), $attribute, $default);
    }

    public function timestamp(): TimestampContract
    {
        return $this->timestamp;
    }

    public function save(): Contract
    {
        return $this->toBase()->save();
    }

    public static function fromBase(Event $event): Contract
    {
        return call_user_func_array([$event->type, 'hydrate'], [
            $event->payload(),
            $event->timestamp(),
            $event->aggregate(),
            $event->id,
        ]);
    }

    public function toBase(): Event
    {
        return Event::make(
            $this->aggregate() ? get_class($this->aggregate()) : Aggregate::class,
            $this->aggregate() ? $this->aggregate()->uuid()->value() : new UUID(),
            get_class($this),
            $this->payload(),
            $this->timestamp()->seconds(),
            $this->id
        );
    }
}
