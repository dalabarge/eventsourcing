<?php

namespace App\Truck\Aggregates;

use App\Carrier\Aggregates\Carrier;
use App\Carrier\Queries\FindByUSDOT;
use App\Carrier\Values\USDOT;
use App\Store\Aggregates\Base as Aggregate;
use App\Truck\Commands\Repaint;
use App\Truck\Commands\ReportAccident;
use App\Truck\Commands\UpdateUnit;
use App\Truck\Events\ColorChanged;
use App\Truck\Events\Repainted;
use App\Truck\Events\TagRegistered;
use App\Truck\Events\UnitUpdated;
use App\Truck\Events\USDOTAssigned;
use App\Truck\Events\VINAssigned;
use App\Truck\Values\VIN;
use Illuminate\Support\Fluent;

class Truck extends Aggregate
{
    public function vin(): VIN
    {
        return new VIN($this->eventByType(VINAssigned::class)
            ->getPayload('vin'));
    }

    public function color(): string
    {
        return $this->eventByType([ColorChanged::class, Repainted::class])
            ->getPayload('color');
    }

    public function usdot(): USDOT
    {
        $usdot = $this->eventByType(USDOTAssigned::class)
            ->getPayload('usdot');

        return $usdot instanceof USDOT ? $usdot : new USDOT((int) $usdot);
    }

    public function carrier(): Carrier
    {
        return app(FindByUSDOT::class)
            ->usdot($this->usdot())
            ->get();
    }

    public function unit(): int
    {
        return $this->eventByType(UnitUpdated::class)
            ->getPayload('unit');
    }

    public function tag(): Fluent
    {
        return new Fluent($this->eventByType(TagRegistered::class)
            ->getPayload('tag'));
    }

    public function repaint(string $color)
    {
        return app(Repaint::class)
            ->truck($this)
            ->color($color)
            ->run();
    }

    public function reportAccident(string $date)
    {
        return app(ReportAccident::class)
            ->truck($this)
            ->date($date)
            ->run();
    }

    public function updateUnit(int $unit)
    {
        return app(UpdateUnit::class)
            ->truck($this)
            ->unit($unit)
            ->run();
    }
}
