<?php

namespace App\Store\Values;

use App\Store\Contracts\Timestamp as Contract;
use Carbon\Carbon;
use JsonSerializable;

class Timestamp implements Contract, JsonSerializable
{
    protected $value;

    public function __construct($value = null)
    {
        if (is_null($value)) {
            $value = Carbon::now();
        }

        if ($value instanceof Contract || is_string($value)) {
            $value = Carbon::parse((string) $value);
        }

        if (is_integer($value)) {
            $value = Carbon::createFromTimestamp($value);
        }

        $this->value = $value;
    }

    public function format($format): string
    {
        return $this->value->format($format);
    }

    public function seconds(): int
    {
        return (int) $this->format('U');
    }

    public function milliseconds(): int
    {
        return (int) $this->format('U.v') * 1000;
    }

    public function value()
    {
        return $this->value->format('Y-m-d H:i:s');
    }

    public function __toString(): string
    {
        return $this->value();
    }

    public function jsonSerialize()
    {
        return $this->seconds();
    }
}
