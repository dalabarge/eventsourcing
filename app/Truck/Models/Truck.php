<?php

namespace App\Truck\Models;

use App\Carrier\Models\Carrier;
use App\Store\Contracts\Aggregate as AggregateContract;
use App\Store\Contracts\Aggregateable;
use App\Store\Values\UUID;
use App\Truck\Aggregates\Truck as Aggregate;
use App\Truck\Values\VIN;
use Illuminate\Database\Eloquent\Model;

class Truck extends Model implements Aggregateable
{
    protected $table = 'trucks';

    protected $fillable = [
        'color',
        'expires_at',
        'lpn',
        'make',
        'model',
        'region',
        'unit',
        'uuid',
        'vin',
        'year',
    ];

    protected $dates = [
        'expires_at',
    ];

    public function carriers()
    {
        return $this->belongsToMany(Carrier::class, 'fleets', 'truck_uuid', 'carrier_uuid', 'uuid', 'uuid');
    }

    public function scopeUuid($query, UUID $uuid)
    {
        return $query->where('uuid', $uuid->value());
    }

    public function scopeVin($query, VIN $vin)
    {
        return $query->where('vin', $vin->value());
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
