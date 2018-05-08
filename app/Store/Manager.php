<?php

namespace App\Store;

use App\Store\Contracts\Aggregate;
use App\Store\Contracts\Builder;
use App\Store\Contracts\Event;
use App\Store\Contracts\Snapshot;
use App\Store\Contracts\Store as Contract;
use App\Store\Contracts\Stream;
use App\Store\Models\Event as Model;
use Illuminate\Database\Eloquent\Builder as Query;
use Illuminate\Support\Collection;

class Manager implements Contract
{
    protected $builder;
    protected $stream;
    protected $snapshot;
    protected $model;

    public function __construct(Builder $builder, Stream $stream, Snapshot $snapshot, Model $model)
    {
        $this->builder = $builder;
        $this->stream = $stream;
        $this->snapshot = $snapshot;
        $this->model = $model;
    }

    public function add(Event $event): Builder
    {
        return $this->builder->add($event);
    }

    public function save(Aggregate $aggregate): Stream
    {
        return $this->builder->save($aggregate);
    }

    public function stream(Aggregate $aggregate): Stream
    {
        return $this->stream->get($aggregate);
    }

    public function snapshot(Stream $stream): Collection
    {
        return (new $this->snapshot())->save($stream);
    }

    public function project(string $projector, Stream $stream = null): int
    {
        return app($projector)->project($stream);
    }

    public function find(int $id): Event
    {
        return $this->query()->findOrFail($id)->toBase();
    }

    public function query(): Query
    {
        return $this->model->newQuery();
    }
}
