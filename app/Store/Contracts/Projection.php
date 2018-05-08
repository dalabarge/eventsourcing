<?php

namespace App\Store\Contracts;

interface Projection
{
    public function aggregates(): array;
}
