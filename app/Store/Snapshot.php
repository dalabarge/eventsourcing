<?php

namespace App\Store;

use App\Store\Contracts\Aggregate;
use App\Store\Contracts\Event as EventContract;
use App\Store\Contracts\Snapshot as Contract;
use App\Store\Contracts\Stream as StreamContract;
use App\Store\Contracts\Timestamp as TimestampContract;
use App\Store\Events\Snapshot as SnapshotEvent;
use App\Store\Models\Event;
use App\Store\Models\Snapshot as Model;
use App\Store\Values\Timestamp;
use App\Store\Values\UUID;
use Exception;
use Illuminate\Support\Collection;

class Snapshot implements Contract
{
    protected $attributes = [];

    public function __construct(array $attributes = [])
    {
        $payload = array_get($attributes, 'payload', []);
        $payload = is_string($payload) ? (array) json_decode($payload, true) : $payload;
        $this->attributes = array_merge($attributes, compact('payload'));
    }

    public function __get($attribute)
    {
        return array_get($this->attributes, $attribute);
    }

    public static function make(string $aggregate, string $uuid, int $event_id, $payload = [], int $timestamp, int $id = null)
    {
        return new static([
            'aggregate' => $aggregate,
            'uuid'      => $uuid,
            'event_id'  => $event_id,
            'payload'   => $payload ?? [],
            'timestamp' => $timestamp,
            'id'        => $id,
        ]);
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function aggregate(): ?Aggregate
    {
        return $this->aggregate ? app($this->aggregate, ['uuid' => new UUID($this->uuid)]) : null;
    }

    public function eventId(): int
    {
        return $this->event_id;
    }

    public function payload(): array
    {
        return $this->payload;
    }

    public function timestamp(): TimestampContract
    {
        return new Timestamp($this->timestamp);
    }

    public function save(StreamContract $stream): Collection
    {
        $snapshots = $this->prepare($stream);

        return $snapshots->transform(function (Contract $snapshot) {
            if ($snapshot->id()) {
                throw new Exception('Snapshot already exists and cannot be re-inserted.');
            }

            $aggregate = $snapshot->aggregate();

            $model = Model::create([
                'aggregate' => get_class($aggregate),
                'uuid'      => $aggregate->uuid()->value(),
                'event_id'  => $snapshot->eventId(),
                'payload'   => $snapshot->payload(),
                'timestamp' => $snapshot->timestamp(),
            ]);

            return $model->toBase();
        });
    }

    public function stream(): StreamContract
    {
        $events = Event::aggregate($this->aggregate())
            ->after($this->eventId())
            ->orderBy('timestamp', 'asc')
            ->get();

        $events->prepend($this->toEvent());

        return new Stream($events);
    }

    public static function find($id): Contract
    {
        return Model::findOrFail($id)->toBase();
    }

    public function toBase(): Contract
    {
        return $this;
    }

    public function toEvent(): EventContract
    {
        return new SnapshotEvent(
            $this->payload(),
            $this->timestamp(),
            $this->aggregate(),
            $this->eventId()
        );
    }

    protected function prepare(Stream $stream): Collection
    {
        $snapshots = collect();

        $stream->events()->each(function ($event) use (&$snapshots) {
            $base = $event->toBase();
            $aggregate = $base->aggregate();
            $payload = $base->payload();

            $key = md5(get_class($aggregate).':'.$aggregate->uuid()->value());
            if ($snapshot = $snapshots->get($key)) {
                $payload = array_merge($snapshot->payload(), $payload);
            }

            $snapshot = static::make(
                get_class($aggregate),
                $aggregate->uuid()->value(),
                $base->id(),
                $payload,
                $base->timestamp()->seconds()
            );

            $snapshots->put($key, $snapshot);
        });

        return $snapshots;
    }
}
