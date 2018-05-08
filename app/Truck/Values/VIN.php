<?php

namespace App\Truck\Values;

use App\Contracts\Value;
use Illuminate\Support\Fluent;
use InvalidArgumentException;
use JsonSerializable;

class VIN implements Value, JsonSerializable
{
    const VALID_PATTERN = '^[a-zA-Z0-9]{16,17}$';

    protected $value;

    public function __construct(string $value)
    {
        $this->value = $this->validate($value);
    }

    public function validate($value)
    {
        $length = strlen($value);

        if ($length < 16 || $length > 17) {
            throw new InvalidArgumentException('A VIN should be 16 or 17 digits.');
        }

        return $value;
    }

    public function value()
    {
        return $this->value;
    }

    public function decode(): Fluent
    {
        // An example of using the VIN to parse and decode encoded information
        // or using a value object to make a call to an external API to fetch
        // in additional data as needed. This has been hard-coded for completness
        // of the example but this should be dynamic.
        return new Fluent([
            'make'  => 'Ford',
            'model' => 'F-150',
            'year'  => date('Y'),
        ]);
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
