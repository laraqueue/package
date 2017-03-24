<?php

use Laraqueue\Facades\Laraqueue;

if (!function_exists('array_recursive_unset')) {
    /**
     * Recursively unsets a key from any array.
     *
     * @param array $array
     * @param mixed $key
     * @return array
     */
    function array_recursive_unset(array &$array, $key) {
        unset($array[$key]);

        foreach ($array as &$value) {
            if (is_array($value)) {
                array_recursive_unset($value, $key);
            }
        }

        return $array;
    }
}

if (! function_exists('laraqueue')) {
    /**
     * Dispatches a job using Laraqueue's dispatcher.
     *
     * @param mixed $job
     * @return int
     */
    function laraqueue($job)
    {
        return Laraqueue::dispatch($job);
    }
}
