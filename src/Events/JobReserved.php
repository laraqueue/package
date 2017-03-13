<?php

namespace Laraqueue\Events;

/**
 * Class JobReserved
 *
 * @package Laraqueue\Events
 */
class JobReserved
{

    public $job;

    /**
     * JobReserved constructor.
     *
     * @param mixed $job
     */
    public function __construct($job)
    {
        $this->job = $job;
    }

}
