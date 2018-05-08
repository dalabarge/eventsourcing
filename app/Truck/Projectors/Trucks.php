<?php

namespace App\Truck\Projectors;

use App\Store\Contracts\Aggregate as Contract;
use App\Store\Contracts\Projector;
use App\Store\Contracts\Store;
use App\Store\Projectors\Base;
use App\Truck\Aggregates\Truck as Aggregate;
use App\Truck\Models\Truck as Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class Trucks extends Base implements Projector
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
        $vin = $aggregate->vin();
        $model = new $this->model();
        $model->uuid = $aggregate->uuid();
        $model->vin = $vin->value();
        $model->color = $aggregate->color();
        $model->unit = $aggregate->unit();

        $tag = $aggregate->tag();
        $model->lpn = $tag->number;
        $model->region = $tag->region;
        $model->expires_at = Carbon::createFromFormat('Y-m-d', $tag->expires)->endOfDay();

        $model->fill($vin->decode()->toArray());

        return $model;
    }
}
