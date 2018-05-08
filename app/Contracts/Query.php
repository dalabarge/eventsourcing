<?php

namespace App\Contracts;

interface Query extends Invokable
{
    public function get();
}
