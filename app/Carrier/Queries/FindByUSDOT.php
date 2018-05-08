<?php

namespace App\Carrier\Queries;

use App\Carrier\Models\Carrier;
use App\Carrier\Values\USDOT;
use App\Contracts\Query;
use App\Store\Concerns\Helpers;
use App\Store\Contracts\Aggregate;

/**
 * @example (new FindByUSDOT($store))
 *              ->usdot(1234567)
 *              ->get()
 */
class FindByUSDOT implements Query
{
    use Helpers;

    protected $arguments = [
        'usdot' => null,
    ];

    public function get(): Aggregate
    {
        $usdot = $this->usdot();

        if ( ! $usdot instanceof USDOT) {
            $usdot = new USDOT($usdot);
        }

        return Carrier::usdot($usdot)
            ->firstOrFail()
            ->toAggregate();
    }
}
