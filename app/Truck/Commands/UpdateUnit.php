<?php

namespace App\Truck\Commands;

use App\Contracts\Command;
use App\Store\Concerns\Helpers;
use App\Store\Contracts\Stream;
use App\Truck\Events\UnitUpdated;

/**
 * @example (new UpdateUnit($store))
 *              ->truck($aggregate)
 *              ->unit(1)
 *              ->run()
 */
class UpdateUnit implements Command
{
    use Helpers;

    protected $arguments = [
        'truck' => null,
        'unit'  => null,
    ];

    public function run(): Stream
    {
        $aggregate = $this->truck()->toAggregate();
        $unit = $this->unit();

        return $this->store
            ->add(new UnitUpdated($unit))
            ->save($aggregate);
    }
}
