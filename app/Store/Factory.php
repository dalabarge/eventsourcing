<?php

namespace App\Store;

use App\Store\Models\Event as Model;

class Factory
{
    public static function make(): Manager
    {
        return new Manager(new Builder(), new Stream(), new Snapshot(), new Model());
    }
}
