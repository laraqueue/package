<?php

namespace Laraqueue\Bus;

use Laraqueue\Support\Sender;
use Illuminate\Queue\SyncQueue;
use Laraqueue\Traits\InteractsWithLaraqueue;

/**
 * Class Dispatcher
 *
 * @package Laraqueue\Bus
 */
class Dispatcher extends \Illuminate\Bus\Dispatcher
{

    use InteractsWithLaraqueue;

    /**
     * @const int
     */
    const DELAY = 5;

    /**
     * Dispatch a command to its appropriate handler.
     *
     * @param mixed $command
     * @return mixed
     */
    public function dispatch($command)
    {
        if($this->isSync($command) || !$this->commandShouldBeQueued($command)) {
            return parent::dispatch($command);
        }

        return $this->handleAsync($command);
    }

    /**
     * Handles asynchronous job.
     *
     * @param mixed $command
     * @return mixed
     */
    protected function handleAsync($command)
    {
        // Delay the job to avoid a race condition with reporting to Laraqueue.
        if(!$command->delay) {
            $command->delay(self::DELAY);
        }

        $command->id = parent::dispatch($command);

        app(Sender::class)->reportJob($command);

        return $command->id;
    }

    /**
     * Validates command is dispatched on sync queue.
     *
     * @param mixed $command
     * @return bool
     */
    protected function isSync($command)
    {
        $connection = isset($command->connection) ? $command->connection : null;

        return call_user_func($this->queueResolver, $connection) instanceof SyncQueue;
    }

}