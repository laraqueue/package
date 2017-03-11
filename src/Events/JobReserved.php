<?php

namespace Laraqueue\Events;

class JobReserved
{

    public $job;

    public function __construct($job)
    {
        $this->job = $job;
    }

}
