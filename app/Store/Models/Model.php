<?php

namespace App\Store\Models;

use App\Store\Contracts\Aggregate;
use App\Store\Contracts\Aggregateable;
use App\Store\Contracts\Timestamp as TimestampContract;
use App\Store\Contracts\UUID as UUIDContract;
use App\Store\Values\Timestamp;
use App\Store\Values\UUID;
use Illuminate\Database\Eloquent\Model as Eloquent;

abstract class Model extends Eloquent implements Aggregateable
{
    public $timestamps = false;

    protected $casts = [
        'payload' => 'json',
    ];

    public function scopeAggregate($query, $aggregate)
    {
        if ($aggregate instanceof Aggregate) {
            return $query->where('aggregate', get_class($aggregate))
                ->where('uuid', $aggregate->uuid()->value());
        }

        return $query->where('aggregate', $aggregate);
    }

    public function getPayloadAttribute(): array
    {
        return (array) $this->fromJson(array_get($this->attributes, 'payload'));
    }

    public function getUuidAttribute(): UUIDContract
    {
        return new UUID(array_get($this->attributes, 'uuid'));
    }

    public function setUuidAttribute($uuid)
    {
        $uuid = $uuid instanceof UUIDInterface ? $uuid : new UUID($uuid);

        array_set($this->attributes, 'uuid', $uuid->value());
    }

    public function getTimestampAttribute(): TimestampContract
    {
        return new Timestamp(array_get($this->attributes, 'timestamp'));
    }

    public function setTimestampAttribute($timestamp)
    {
        $timestamp = $timestamp instanceof TimestampInterface ? $timestamp : new Timestamp($timestamp);

        array_set($this->attributes, 'timestamp', $timestamp->value());
    }

    public function getPayload($attribute, $default = null)
    {
        return array_get($this->payload, $attribute, $default);
    }

    public function toAggregate(): Aggregate
    {
        $aggregate = $this->aggregate;

        return new $aggregate($this->uuid);
    }
}
