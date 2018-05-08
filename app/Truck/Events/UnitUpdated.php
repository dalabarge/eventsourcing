<?php

namespace App\Truck\Events;

use App\Store\Contracts\Aggregate;
use App\Store\Events\Base;

class UnitUpdated extends Base
{
    public function __construct(int $unit, ...$args)
    {
        parent::__construct(compact('unit'), ...$args);
    }

    public static function hydrate(array $payload = [], $timestamp = null, Aggregate $aggregate = null, int $id = null)
    {
        return new static(array_get($payload, 'unit'), $timestamp, $aggregate, $id);
    }
}
