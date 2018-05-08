<?php

namespace App\Store\Contracts;

interface Event
{
    public function id(): ?int;

    public function aggregate(): ?Aggregate;

    public function payload(): array;

    public function timestamp(): Timestamp;

    public function save(): Event;
}
