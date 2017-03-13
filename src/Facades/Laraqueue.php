<?php

namespace Laraqueue\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Laraqueue
 *
 * @package Laraqueue\Facades
 */
class Laraqueue extends Facade
{

    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Laraqueue';
    }

}
