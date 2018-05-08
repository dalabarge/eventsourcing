<?php

namespace App\Carrier\Queries;

use App\Contracts\Query;
use App\Store\Concerns\Helpers;
use Illuminate\Support\Fluent;

/**
 * @example (new GetCarrierProfile($store))
 *              ->usdot(1234567)
 *              ->get()
 */
class GetCarrierProfile implements Query
{
    use Helpers;

    protected $arguments = [
        'usdot' => null,
    ];

    public function get(): Fluent
    {
        // This is an example of a query that would hit
        // an external API to query in the data based
        // on the UUID. We're just stubbing a hard-coded
        // return in stead for completeness.
        return new Fluent([
            'active'     => true,
            'drivers'    => 3,
            'interstate' => true,
            'name'       => 'Acme Transport Co.',
        ]);
    }
}
