<?php

namespace App\Store\Concerns;

use App\Contracts\Query;
use App\Store\Contracts\Store;

trait Helpers
{
    protected $store;

    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    public function __invoke()
    {
        return $this instanceof Query ? $this->get() : $this->run();
    }

    public function __call($method, $arguments)
    {
        if (empty($arguments)) {
            return array_get($this->arguments, $method);
        }

        array_set($this->arguments, $method, head($arguments));

        return $this;
    }
}
