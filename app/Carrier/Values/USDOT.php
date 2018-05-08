<?php

namespace App\Carrier\Values;

use App\Contracts\Value;
use InvalidArgumentException;
use JsonSerializable;

class USDOT implements Value, JsonSerializable
{
    const VALID_PATTERN = '^[0-9]{7}$';

    protected $value;

    public function __construct(int $value)
    {
        $this->value = $this->validate($value);
    }

    public function validate(int $value)
    {
        $length = strlen((string) $value);

        if ($length > 7) {
            throw new InvalidArgumentException('A USDOT should be 7 or fewer digits.');
        }

        return $value;
    }

    public function value()
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value();
    }

    public function jsonSerialize()
    {
        return $this->__toString();
    }
}
