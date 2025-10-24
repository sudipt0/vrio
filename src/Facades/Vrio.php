<?php

namespace Sudipta\Vrio\Facades;

use Illuminate\Support\Facades\Facade;

class Vrio extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Sudipta\Vrio\VrioClient::class;
    }
}
