<?php

namespace App\Truck\Models;

use App\Carrier\Models\Carrier;
use App\Carrier\Values\USDOT;
use App\Store\Contracts\Aggregate;
use App\Store\Contracts\Aggregateable;
use App\Store\Contracts\Projection;
use App\Store\Values\UUID;
use App\Truck\Aggregates\Truck as TruckAggregate;
use App\Truck\Values\VIN;
use Illuminate\Database\Eloquent\Model;

class Fleet extends Model implements Aggregateable, Projection
{
    protected $table = 'fleets';

    protected $fillable = [
        'active',
        'carrier_uuid',
        'color',
        'drivers',
        'expires_at',
        'interstate',
        'lpn',
        'make',
        'model',
        'name',
        'region',
        'truck_uuid',
        'trucks',
        'unit',
        'usdot',
        'uuid',
        'vin',
        'year',
    ];

    protected $dates = [
        'expires_at',
    ];

    public function carrier()
    {
        return $this->belongsTo(Carrier::class, 'carrier_uuid', 'uuid');
    }

    public function truck()
    {
        return $this->belongsTo(Truck::class, 'truck_uuid', 'uuid');
    }

    public function scopeVin($query, VIN $vin)
    {
        return $query->where('vin', $vin->value());
    }

    public function scopeUsdot($query, USDOT $usdot)
    {
        return $query->where('usdot', $usdot->value());
    }

    public function aggregates(): array
    {
        return [
            'truck'   => $this->truck->toAggregate(),
            'carrier' => $this->carrier->toAggregate(),
        ];
    }

    public function toAggregate(): Aggregate
    {
        // An example of a projection that has multiple aggregate roots.
        // This model however is best represented by the truck aggregate
        // and so is aggregateable to the truck as the principle aggregate.
        return new TruckAggregate(new UUID($this->truck));
    }
}
