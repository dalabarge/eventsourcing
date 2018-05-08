<?php

namespace App\Truck\Events;

use App\Store\Contracts\Aggregate;
use App\Store\Events\Base;
use Carbon\Carbon;

class TagRegistered extends Base
{
    public function __construct(string $number, $expires, $region, ...$args)
    {
        // An example of converting from data shorthand to extended value
        if ( ! $expires instanceof Carbon) {
            $expires = Carbon::createFromFormat('m/Y', $expires)->endOfMonth();
        }
        $expires = $expires->format('Y-m-d');

        // An example of mangling event data to normalized values
        // You should probably use a smarter value object for region instead
        if (5 !== strlen($region)) {
            $region = 'US-'.$region;
        }

        $tag = compact('number', 'expires', 'region');

        parent::__construct(compact('tag'), ...$args);
    }

    public static function hydrate(array $payload = [], $timestamp = null, Aggregate $aggregate = null, int $id = null)
    {
        $tag = array_get($payload, 'tag');
        $number = array_get($tag, 'number');
        $region = array_get($tag, 'region');

        // We convert this here so that the constructor doesn't try to parse the wrong format
        $expires = Carbon::createFromFormat('Y-m-d', array_get($tag, 'expires'))->endOfDay();

        return new static($number, $expires, $region, $timestamp, $aggregate, $id);
    }
}
