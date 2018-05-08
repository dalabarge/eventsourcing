<?php

namespace App\Store;

use App\Store\Contracts\Aggregate;
use App\Store\Contracts\Event as Contract;
use App\Store\Contracts\Timestamp as TimestampContract;
use App\Store\Models\Event as Model;
use App\Store\Values\Timestamp;
use App\Store\Values\UUID;
use Exception;

class Event implements Contract
{
    protected $attributes = [];

    public function __construct(array $attributes = [])
    {
        $payload = array_get($attributes, 'payload', []);
        $payload = is_string($payload) ? (array) json_decode($payload, true) : $payload;
        $this->attributes = array_merge($attributes, compact('payload'));
    }

    public function __get($attribute)
    {
        return array_get($this->attributes, $attribute);
    }

    public static function make(string $aggregate, string $uuid, string $type, $payload = [], int $timestamp = null, int $id = null)
    {
        return new static([
            'aggregate' => $aggregate,
            'uuid'      => $uuid,
            'type'      => $type,
            'payload'   => $payload ?? [],
            'timestamp' => $timestamp,
            'id'        => $id,
        ]);
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
        return $this->aggregate ? app($this->aggregate, ['uuid' => new UUID($this->uuid)]) : null;
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
        return new Timestamp($this->timestamp);
    }

    public function save(): Contract
    {
        if ($this->id()) {
            throw new Exception('Event already exists and cannot be re-inserted.');
        }

        $event = Model::create([
            'aggregate' => $this->aggregate,
            'uuid'      => $this->uuid,
            'type'      => $this->type,
            'payload'   => $this->payload(),
            'timestamp' => $this->timestamp(),
        ]);

        return $event->toBase();
    }

    public function toBase(): Contract
    {
        return $this;
    }
}
