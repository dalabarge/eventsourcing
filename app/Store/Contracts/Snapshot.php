<?php

namespace App\Store\Contracts;

use Illuminate\Support\Collection;

interface Snapshot
{
    public function id(): ?int;

    public function aggregate(): ?Aggregate;

    public function eventId(): int;

    public function payload(): array;

    public function timestamp(): Timestamp;

    public function save(Stream $stream): Collection;

    public function stream(): Stream;

    public static function find($id): Snapshot;
}
