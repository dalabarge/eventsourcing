<?php

namespace App\Truck\Events;

use App\Store\Contracts\Aggregate;
use App\Store\Events\Base;

class ColorChanged extends Base
{
    const ALLOWED_COLORS = [
        'black',
        'blue',
        'green',
        'red',
        'white',
    ];

    public function __construct(string $color, ...$args)
    {
        $color = $this->validate($color);

        parent::__construct(compact('color'), ...$args);
    }

    public static function hydrate(array $payload = [], $timestamp = null, Aggregate $aggregate = null, int $id = null)
    {
        return new static(array_get($payload, 'color'), $timestamp, $aggregate, $id);
    }

    public function validate(string $color): string
    {
        // This logic would be better moved to a value object but it remains
        // here as a demonstration that events can be self-validating
        if ( ! in_array($color, static::ALLOWED_COLORS)) {
            throw new InvalidArgumentException('The color "'.$color.'" is not one of the allowed colors: '.implode(', ', static::ALLOWED_COLORS));
        }

        return $color;
    }
}
