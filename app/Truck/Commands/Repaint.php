<?php

namespace App\Truck\Commands;

use App\Contracts\Command;
use App\Store\Concerns\Helpers;
use App\Store\Contracts\Stream;
use App\Truck\Events\Repainted;

/**
 * @example (new Repaint($store))
 *              ->truck($aggregate)
 *              ->color('red')
 *              ->run()
 */
class Repaint implements Command
{
    use Helpers;

    protected $arguments = [
        'truck' => null,
        'color' => null,
    ];

    public function run(): Stream
    {
        $aggregate = $this->truck()->toAggregate();
        $color = $this->color();

        return $this->store
            ->add(new Repainted($color))
            ->save($aggregate);
    }
}
