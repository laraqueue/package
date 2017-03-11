<?php

namespace Laraqueue\Facades;

use Illuminate\Support\Facades\Facade;

class Laraqueue extends Facade
{

    protected static function getFacadeAccessor() {
        return 'Laraqueue';
    }

}
