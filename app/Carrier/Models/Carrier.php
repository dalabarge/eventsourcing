<?php

namespace App\Carrier\Models;

use App\Carrier\Aggregates\Carrier as Aggregate;
use App\Carrier\Values\USDOT;
use App\Store\Contracts\Aggregate as AggregateContract;
use App\Store\Values\UUID;
use App\Truck\Models\Truck;
use Illuminate\Database\Eloquent\Model;

class Carrier extends Model
{
    protected $table = 'carriers';

    protected $fillable = [
        'active',
        'drivers',
        'interstate',
        'name',
        'trucks',
        'usdot',
        'uuid',
    ];

    public function trucks()
    {
        return $this->belongsToMany(Truck::class, 'fleets', 'carrier_uuid', 'truck_uuid', 'uuid', 'uuid');
    }

    public function scopeUuid($query, UUID $uuid)
    {
        return $query->where('uuid', $uuid->value());
    }

    public function scopeUsdot($query, USDOT $usdot)
    {
        return $query->where('usdot', $usdot->value());
    }

    public function aggregates(): array
    {
        return [$this->toAggregate()];
    }

    public function toAggregate(): AggregateContract
    {
        return new Aggregate(new UUID($this->uuid));
    }
}
