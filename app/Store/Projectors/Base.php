<?php

namespace App\Store\Projectors;

use App\Store\Contracts\Aggregate;
use App\Store\Contracts\Projector;
use App\Store\Contracts\Store;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

abstract class Base implements Projector
{
    protected $store;
    protected $model;

    public function __construct(Store $store, Model $model)
    {
        $this->store = $store;
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
        return $this->store->query()
            ->select('aggregate', 'uuid')
            ->aggregate(Aggregate::class)
            ->groupBy(['aggregate', 'uuid']);
    }

    public function save(Aggregate $aggregate)
    {
        $model = $this->map($aggregate);
        $attributes = $model->toArray();

        return $this->model->updateOrCreate([
            'uuid' => $aggregate->uuid()->value(),
        ], $attributes);
    }

    public function map(Aggregate $aggregate)
    {
        $model = new $this->model();
        $model->uuid = $aggregate->uuid();

        return $model;
    }
}
