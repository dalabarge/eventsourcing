<?php

namespace App\Store\Contracts;

use Illuminate\Support\Collection;

interface Builder
{
    public function events(): Collection;

    public function add(Event $event): Builder;

    public function reset(): Builder;

    public function save(Aggregate $aggregate): Stream;
}
