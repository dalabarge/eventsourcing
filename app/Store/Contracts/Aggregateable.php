<?php

namespace App\Store\Contracts;

interface Aggregateable
{
    public function toAggregate(): Aggregate;
}
