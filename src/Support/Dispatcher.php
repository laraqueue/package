<?php

namespace Laraqueue\Support;

use Laraqueue\Traits\InteractsWithLaraqueue;

class Dispatcher
{

    use InteractsWithLaraqueue;

    public function dispatch($job)
    {
        if($this->isSync($job)) {
            return $this->handleSync($job);
        }

        return $this->handleAsync($job);
    }

    protected function handleSync($job)
    {
        return dispatch($job);
    }

    protected function handleAsync($job)
    {
        $job->id = dispatch($job);

        if($this->getToken()) {
            $this->sendJob($job);
        }

        return $job->id;
    }

    protected function sendJob($job)
    {
        app(Sender::class)->sendJob($job);
    }

}
