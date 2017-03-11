<?php

use Laraqueue\Facades\Laraqueue;

if (! function_exists('laraqueue')) {
    function laraqueue($job)
    {
        return Laraqueue::dispatch($job);
    }
}
