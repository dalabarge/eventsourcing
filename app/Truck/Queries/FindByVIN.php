<?php

namespace App\Truck\Queries;

use App\Contracts\Query;
use App\Store\Concerns\Helpers;
use App\Store\Contracts\Aggregate;
use App\Truck\Models\Truck;
use App\Truck\Values\VIN;

/**
 * @example (new FindByVIN($store))
 *              ->vin('12345678901234567')
 *              ->get()
 */
class FindByVIN implements Query
{
    use Helpers;

    protected $arguments = [
        'vin' => null,
    ];

    public function get(): Aggregate
    {
        $vin = $this->vin();

        if ( ! $vin instanceof VIN) {
            $vin = new VIN($vin);
        }

        return Truck::vin($vin)
            ->firstOrFail()
            ->toAggregate();
    }
}
