<?php

namespace App\Store\Contracts;

use Illuminate\Database\Eloquent\Builder as Query;
use Illuminate\Support\Collection;

interface Store
{
    public function add(Event $event): Builder;

    public function stream(Aggregate $aggregate): Stream;

    public function save(Aggregate $aggregate): Stream;

    public function snapshot(Stream $stream): Collection;

    public function project(string $projector, Stream $stream = null): int;

    public function find(int $id): Event;

    public function query(): Query;
}
