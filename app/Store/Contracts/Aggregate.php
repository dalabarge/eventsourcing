<?php

namespace App\Store\Contracts;

interface Aggregate
{
    public function uuid(): UUID;

    public function stream(): Stream;

    public function snapshot(): Snapshot;

    public function add(Event $event): Builder;
}
