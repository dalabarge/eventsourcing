<?php

namespace App\Truck\Commands;

use App\Contracts\Command;
use App\Store\Concerns\Helpers;
use App\Store\Contracts\Stream;
use App\Truck\Events\AccidentReported;
use Carbon\Carbon;

/**
 * @example (new ReportAccident($store))
 *              ->truck($aggregate)
 *              ->date('2018-04-01')
 *              ->run()
 */
class ReportAccident implements Command
{
    use Helpers;

    protected $arguments = [
        'truck' => null,
        'date'  => 1,
    ];

    public function run(): Stream
    {
        $aggregate = $this->truck()->toAggregate();
        $date = $this->date() ?? Carbon::now();

        return $this->store
            ->add(new AccidentReported($date))
            ->save($aggregate);
    }
}
