<?php

namespace App\Truck\Events;

use App\Store\Contracts\Aggregate;
use App\Store\Contracts\UUID as UUIDContract;
use App\Store\Events\Base;
use App\Store\Values\UUID;

class Created extends Base
{
    public function __construct($uuid, ...$args)
    {
        if ( ! $uuid instanceof UUIDContract) {
            $uuid = new UUID($uuid);
        }

        parent::__construct(compact('uuid'), ...$args);
    }

    public static function hydrate(array $payload = [], $timestamp = null, Aggregate $aggregate = null, int $id = null)
    {
        return new static(array_get($payload, 'uuid'), $timestamp, $aggregate, $id);
    }
}
