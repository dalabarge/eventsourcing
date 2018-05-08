<?php

namespace App\Store\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface Projector
{
    public function project(): int;

    public function query(): Builder;

    public function save(Aggregate $aggregate);

    public function map(Aggregate $aggregate);
}
