<?php

namespace App\Truck\Events;

use App\Carrier\Values\USDOT;
use App\Store\Contracts\Aggregate;
use App\Store\Events\Base;

class USDOTAssigned extends Base
{
    public function __construct($usdot, ...$args)
    {
        if ($usdot instanceof Aggregate) {
            $usdot = $usdot->usdot();
        }

        if ( ! $usdot instanceof USDOT) {
            $usdot = new USDOT($usdot);
        }

        parent::__construct(compact('usdot'), ...$args);
    }

    public static function hydrate(array $payload = [], $timestamp = null, Aggregate $aggregate = null, int $id = null)
    {
        return new static(array_get($payload, 'usdot'), $timestamp, $aggregate, $id);
    }
}
