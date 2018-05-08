<?php

namespace App\Truck\Projectors;

use App\Carrier\Projectors\Carriers;
use App\Store\Contracts\Aggregate as Contract;
use App\Store\Contracts\Projector;
use App\Truck\Models\Fleet as Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Fleets implements Projector
{
    protected $trucks;
    protected $carriers;
    protected $model;

    public function __construct(Trucks $trucks, Carriers $carriers, Model $model)
    {
        $this->trucks = $trucks;
        $this->carriers = $carriers;
        $this->model = $model;
    }

    public function project(): int
    {
        $events = $this->query()->get();

        DB::beginTransaction();

        $events->each(function ($event) {
            $this->save($event->toAggregate());
        });

        DB::commit();

        return $events->count();
    }

    public function query(): Builder
    {
        return $this->trucks->query();
    }

    public function save(Contract $aggregate)
    {
        $model = $this->map($aggregate);
        $attributes = $model->toArray();

        return $this->model->updateOrCreate([
            'truck_uuid'   => (string) $model->truck_uuid,
            'carrier_uuid' => (string) $model->carrier_uuid,
        ], $attributes);
    }

    public function map(Contract $aggregate): Model
    {
        $truck = $this->trucks->map($aggregate);
        $carrier = $this->carriers->map($aggregate->carrier());

        $model = new Model();
        $model->truck_uuid = $truck->uuid;
        $model->carrier_uuid = $carrier->uuid;
        $model->fill(array_except($truck->toArray(), 'uuid'));
        $model->fill(array_except($carrier->toArray(), 'uuid', 'created_at', 'updated_at'));

        return $model;
    }
}
