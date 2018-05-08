<?php

namespace App\Truck\Events;

use App\Store\Contracts\Aggregate;
use App\Store\Contracts\Timestamp;
use App\Store\Events\Base;
use Carbon\Carbon;

class AccidentReported extends Base
{
    public function __construct($date, $timestamp = null, ...$args)
    {
        // An example of using a payload date to set the event timestamp.
        // This effectively inserts the event historically into the event stream.
        if ( ! $timestamp) {
            if ( ! $date instanceof Timestamp && ! $date instanceof Carbon) {
                $date = Carbon::createFromFormat('Y-m-d', $date)->startOfDay();
            }
            $timestamp = $date;
        }

        if ($date instanceof Carbon || $date instanceof Timestamp) {
            $date = $date->format('Y-m-d');
        }

        $accident = compact('date');

        parent::__construct(compact('accident'), $timestamp, ...$args);
    }

    public static function hydrate(array $payload = [], $timestamp = null, Aggregate $aggregate = null, int $id = null)
    {
        $accident = array_get($payload, 'accident');
        $date = array_get($accident, 'date');

        return new static($date, $timestamp, $aggregate, $id);
    }
}
