<?php

namespace App\Carrier\Events;

use App\Store\Contracts\Aggregate;
use App\Store\Contracts\UUID as UUIDContract;
use App\Store\Events\Base;
use App\Store\Values\UUID;

class TruckAdded extends Base
{
    public function __construct($truck, ...$args)
    {
        if ($truck instanceof Aggregate) {
            $truck = $truck->uuid();
        }

        if ( ! $truck instanceof UUIDContract) {
            $truck = new UUID($truck);
        }

        parent::__construct(compact('truck'), ...$args);
    }

    public static function hydrate(array $payload = [], $timestamp = null, Aggregate $aggregate = null, int $id = null)
    {
        $truck = array_get($payload, 'truck');

        return new static($truck, $timestamp, $aggregate, $id);
    }
}
