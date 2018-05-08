<?php

namespace App\Contracts;

interface Command extends Invokable
{
    public function run();
}
