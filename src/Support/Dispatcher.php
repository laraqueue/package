<?php

namespace Laraqueue\Support;

use Laraqueue\Traits\InteractsWithLaraqueue;

/**
 * Class Dispatcher
 *
 * @package Laraqueue\Support
 */
class Dispatcher
{

    use InteractsWithLaraqueue;

    /**
     * Dispatches job.
     *
     * @param mixed $job
     * @return int
     */
    public function dispatch($job)
    {
        if($this->isSync($job)) {
            return $this->handleSync($job);
        }

        return $this->handleAsync($job);
    }

    /**
     * Handles asynchronous job.
     *
     * @param mixed $job
     * @return int
     */
    protected function handleAsync($job)
    {
        $job->id = dispatch($job);

        if($this->getKey()) {
            $this->reportJob($job);
        }

        return $job->id;
    }

    /**
     * Handles synchronous job.
     *
     * @param mixed $job
     * @return int
     */
    protected function handleSync($job)
    {
        return dispatch($job);
    }

    public function post($payload)
    {
        return app(Sender::class)->post($payload);
    }

}
