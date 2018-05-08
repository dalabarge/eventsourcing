<?php

namespace App\Store\Values;

use App\Store\Contracts\UUID as Contract;
use JsonSerializable;
use Ramsey\Uuid\Uuid as Factory;

class UUID implements Contract, JsonSerializable
{
    protected $value;

    public function __construct(string $value = null)
    {
        $this->value = $value ?? $this->generate()->value();
    }

    public function generate(): Contract
    {
        return new self(Factory::uuid4()->toString());
    }

    public function value()
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value();
    }

    public function jsonSerialize()
    {
        return $this->__toString();
    }
}
