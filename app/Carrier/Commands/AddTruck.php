<?php

namespace App\Carrier\Commands;

use App\Carrier\Events\TruckAdded;
use App\Carrier\Queries\FindByUSDOT;
use App\Contracts\Command;
use App\Store\Concerns\Helpers;
use App\Store\Contracts\Aggregate;
use App\Store\Contracts\Stream;
use App\Store\Values\Timestamp;
use App\Truck\Events\USDOTAssigned;
use App\Truck\Queries\FindByVIN;

/**
 * @example (new AddTruck($store))
 *              ->usdot(1234567)
 *              ->vin('12345678901234567')
 *              ->run()
 */
class AddTruck implements Command
{
    use Helpers;

    protected $arguments = [
        'usdot' => null,
        'vin'   => null,
    ];

    public function run(): Stream
    {
        $carrier = $this->getCarrierAggregateFromUSDOT($this->usdot());
        $truck = $this->getTruckAggregateFromVIN($this->vin());

        return $this->store
            ->add(new USDOTAssigned($carrier, new Timestamp(), $truck))
            ->add(new TruckAdded($truck))
            ->save($carrier);
    }

    protected function getCarrierAggregateFromUSDOT($usdot): Aggregate
    {
        if ($usdot instanceof Aggregate) {
            return $usdot;
        }

        return (new FindByUSDOT($this->store))
            ->usdot($usdot)
            ->get();
    }

    protected function getTruckAggregateFromVIN($vin): Aggregate
    {
        if ($vin instanceof Aggregate) {
            return $vin;
        }

        return (new FindByVIN($this->store))
            ->vin($vin)
            ->get();
    }
}
