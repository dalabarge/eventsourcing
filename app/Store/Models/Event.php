<?php

namespace App\Store\Models;

use App\Models\Stream;
use App\Store\Contracts\Event as Contract;
use App\Store\Contracts\Stream as StreamContract;
use App\Store\Contracts\Timestamp;
use Carbon\Carbon;
use InvalidArgumentException;

class Event extends Model
{
    protected $table = 'events';

    protected $fillable = [
        'aggregate',
        'uuid',
        'type',
        'payload',
        'timestamp',
    ];

    public function scopeType($query, $types)
    {
        return $query->whereIn('type', (array) $types);
    }

    public function scopeAfter($query, int $id)
    {
        return $query->where('id', '>', $id);
    }

    public function scopeSince($query, $value)
    {
        if (is_int($value)) {
            return $query->where('id', '>=', $value);
        }

        if (is_string($value)) {
            $value = Carbon::parse($value);
        }

        if ($value instanceof Carbon || $value instanceof Timestamp) {
            return $query->where('timestamp', '>=', $value->format('Y-m-d H:i:s'));
        }

        throw new InvalidArgumentException('The value passed to '.__METHOD__.' is not supported.');
    }

    public function scopeUntil($query, $value)
    {
        if (is_int($value)) {
            return $query->where('id', '<=', $value);
        }

        if (is_string($value)) {
            $value = Carbon::parse($value);
        }

        if ($value instanceof Carbon || $value instanceof Timestamp) {
            return $query->where('timestamp', '<=', $value->format('Y-m-d H:i:s'));
        }

        throw new InvalidArgumentException('The value passed to '.__METHOD__.' is not supported.');
    }

    public function stream(): StreamContract
    {
        return new Stream($this->get());
    }

    public function toBase(): Contract
    {
        return call_user_func_array([$this->type, 'hydrate'], [
            $this->payload,
            $this->timestamp,
            app($this->aggregate, ['uuid' => $this->uuid]),
            $this->id,
        ]);
    }
}
