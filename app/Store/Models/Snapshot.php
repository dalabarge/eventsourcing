<?php

namespace App\Store\Models;

use App\Store\Contracts\Snapshot as Contract;
use App\Store\Snapshot as Base;

class Snapshot extends Model
{
    protected $table = 'snapshots';

    protected $fillable = [
        'event_id',
        'aggregate',
        'uuid',
        'payload',
        'timestamp',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function toBase(): Contract
    {
        return Base::make(
            $this->aggregate,
            $this->uuid->value(),
            $this->event_id,
            $this->payload,
            $this->timestamp->seconds(),
            $this->id
        );
    }
}
