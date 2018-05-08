<?php

namespace App\Store\Contracts;

use Illuminate\Support\Collection;

interface Stream
{
    public function events(): Collection;

    public function type($type): Stream;

    public function first(): ?Event;

    public function last(): ?Event;

    public function get(Aggregate $aggregate): Stream;

    public function snapshot(): Collection;
}
