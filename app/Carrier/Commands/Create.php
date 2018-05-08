<?php

namespace App\Carrier\Commands;

use App\Carrier\Aggregates\Carrier;
use App\Carrier\Events\Created;
use App\Carrier\Values\USDOT;
use App\Contracts\Command;
use App\Store\Concerns\Helpers;
use App\Store\Contracts\Stream;
use App\Store\Values\UUID;

/**
 * @example (new Create($store))
 *              ->usdot(1234567)
 *              ->run()
 */
class Create implements Command
{
    use Helpers;

    protected $arguments = [
        'usdot' => null,
    ];

    public function run(): Stream
    {
        $usdot = new USDOT($this->usdot());
        $aggregate = new Carrier(new UUID(), $usdot);

        return $this->store
            ->add(new Created($aggregate->uuid(), $usdot))
            ->save($aggregate);
    }
}
