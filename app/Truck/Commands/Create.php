<?php

namespace App\Truck\Commands;

use App\Contracts\Command;
use App\Store\Concerns\Helpers;
use App\Store\Contracts\Stream;
use App\Store\Values\UUID;
use App\Truck\Aggregates\Truck;
use App\Truck\Events\ColorChanged;
use App\Truck\Events\Created;
use App\Truck\Events\TagRegistered;
use App\Truck\Events\UnitUpdated;
use App\Truck\Events\VINAssigned;
use Carbon\Carbon;

/**
 * @example (new Create($store))
 *              ->vin('12345678901234567')
 *              ->number('LPN 123')
 *              ->expires('04/2018')
 *              ->color('white')
 *              ->region('TX')
 *              ->unit(1)
 *              ->run()
 */
class Create implements Command
{
    use Helpers;

    protected $arguments = [
        'vin'     => null,
        'unit'    => 1,
        'color'   => 'white',
        'number'  => null,
        'expires' => null,
        'region'  => 'TX',
    ];

    public function run(): Stream
    {
        $aggregate = new Truck(new UUID());

        $number = $this->number() ?? $this->generateTag();
        $expires = $this->expires() ?? Carbon::now();

        return $this->store
            ->add(new Created($aggregate->uuid()))
            ->add(new VINAssigned($this->vin()))
            ->add(new UnitUpdated($this->unit()))
            ->add(new ColorChanged($this->color()))
            ->add(new TagRegistered($number, $expires, $this->region()))
            ->save($aggregate);
    }

    protected function generateTag()
    {
        return strtoupper(str_random(3).' '.str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT));
    }
}
