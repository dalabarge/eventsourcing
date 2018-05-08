<?php

namespace App\Store\Contracts;

use App\Contracts\Value;

interface Timestamp extends Value
{
    public function format($format): string;

    public function seconds(): int;

    public function milliseconds(): int;
}
