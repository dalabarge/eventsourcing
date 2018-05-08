<?php

namespace App\Truck\Events;

use App\Store\Contracts\Aggregate;
use App\Store\Events\Base;
use App\Truck\Values\VIN;

class VINAssigned extends Base
{
    public function __construct($vin, ...$args)
    {
        if ( ! $vin instanceof VIN) {
            $vin = new VIN($vin);
        }
        parent::__construct(compact('vin'), ...$args);
    }

    public static function hydrate(array $payload = [], $timestamp = null, Aggregate $aggregate = null, int $id = null)
    {
        return new static(array_get($payload, 'vin'), $timestamp, $aggregate, $id);
    }
}
