<?php

namespace App\Store\Contracts;

use App\Contracts\Value;

interface UUID extends Value
{
    public function generate(): UUID;
}
