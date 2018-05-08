<?php

namespace App\Carrier\Projectors;

use App\Carrier\Aggregates\Carrier as Aggregate;
use App\Carrier\Models\Carrier as Model;
use App\Carrier\Queries\GetCarrierProfile;
use App\Store\Contracts\Aggregate as Contract;
use App\Store\Contracts\Projector;
use App\Store\Contracts\Store;
use App\Store\Projectors\Base;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Fluent;

class Carriers extends Base implements Projector
{
    public function __construct(Store $store, Model $model)
    {
        $this->store = $store;
        $this->model = $model;
    }

    public function query(): Builder
    {
        return $this->store->query()
            ->select('aggregate', 'uuid')
            ->aggregate(Aggregate::class)
            ->groupBy(['aggregate', 'uuid']);
    }

    public function map(Contract $aggregate): Model
    {
        $model = new $this->model();
        $model->uuid = $aggregate->uuid();
        $model->usdot = $aggregate->usdot();
        $model->trucks = $aggregate->trucks()->count();
        $model->fill($this->fetch($model->usdot->value())->toArray());

        return $model;
    }

    protected function fetch($usdot): Fluent
    {
        return (new GetCarrierProfile($this->store))
            ->usdot($usdot)
            ->get();
    }
}
