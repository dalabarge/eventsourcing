<?php

namespace App\Carrier\Aggregates;

use App\Carrier\Commands\AddTruck;
use App\Carrier\Events\Created;
use App\Carrier\Events\TruckAdded;
use App\Carrier\Values\USDOT;
use App\Store\Aggregates\Base as Aggregate;
use App\Store\Values\UUID;
use App\Truck\Aggregates\Truck;
use Illuminate\Support\Collection;

class Carrier extends Aggregate
{
    public function usdot(): USDOT
    {
        $usdot = $this->eventByType(Created::class)->getPayload('usdot');

        return $usdot instanceof USDOT ? $usdot : new USDOT((int) $usdot);
    }

    public function trucks(): Collection
    {
        return $this->stream()
            ->type(TruckAdded::class)
            ->unique(function ($event) {
                return $event->getPayload('truck');
            })
            ->transform(function ($event) {
                return new Truck(new UUID($event->getPayload('truck')));
            })
            ->events();
    }

    public function addTruck(Truck $truck)
    {
        return app(AddTruck::class)
            ->usdot($this)
            ->vin($truck)
            ->run();
    }
}
