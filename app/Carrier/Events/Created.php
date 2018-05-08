<?php

namespace App\Carrier\Events;

use App\Carrier\Values\USDOT;
use App\Store\Contracts\Aggregate;
use App\Store\Contracts\UUID as UUIDContract;
use App\Store\Events\Base;
use App\Store\Values\UUID;

class Created extends Base
{
    public function __construct($uuid, $usdot, ...$args)
    {
        if ( ! $uuid instanceof UUIDContract) {
            $uuid = new UUID($uuid);
        }

        if ( ! $usdot instanceof USDOT) {
            $usdot = new USDOT($usdot);
        }

        parent::__construct(compact('uuid', 'usdot'), ...$args);
    }

    public static function hydrate(array $payload = [], $timestamp = null, Aggregate $aggregate = null, int $id = null)
    {
        $uuid = array_get($payload, 'uuid');
        $usdot = array_get($payload, 'usdot');

        return new static($uuid, $usdot, $timestamp, $aggregate, $id);
    }
}
