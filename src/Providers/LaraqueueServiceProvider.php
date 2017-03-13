<?php

namespace Laraqueue\Providers;

use Laraqueue\Support\Client;
use Laraqueue\Support\Sender;
use Laraqueue\Support\Connector;
use Laraqueue\Support\Dispatcher;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\ServiceProvider;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Laraqueue\Traits\InteractsWithLaraqueue;
use Illuminate\Queue\Events\JobExceptionOccurred;

/**
 * Class LaraqueueServiceProvider
 *
 * @package Laraqueue\Providers
 */
class LaraqueueServiceProvider extends ServiceProvider
{

    use InteractsWithLaraqueue;

    /**
     * Boots service provider.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/laraqueue.php' => config_path('laraqueue.php'),
        ], 'config');

        if(!$this->getKey()) {
            return;
        }

        $this->registerListeners();
    }

    /**
     * Registers service provider.
     */
    public function register()
    {
        $this->app->singleton(Client::class, function() {
            return new Client($this->getKey());
        });
        $this->app->singleton(Sender::class);
        $this->app->bind('Laraqueue', Dispatcher::class);
    }

    /**
     * Registers queue event listeners.
     */
    protected function registerListeners()
    {
        Queue::before(function (JobProcessing $event) {
            $this->handleEvent($event);
        });

        Queue::after(function (JobProcessed $event) {
            $this->handleEvent($event);
        });

        Queue::exceptionOccurred(function (JobExceptionOccurred $event) {
            $this->handleEvent($event);
        });

        Queue::failing(function (JobFailed $event) {
            $this->handleEvent($event);
        });
    }

    /**
     * Handles queue event.
     *
     * @param mixed $event
     */
    protected function handleEvent($event)
    {
        if($this->isSync($event->job)) {
            return;
        }

        $this->app(Sender::class)->sendEvent($event);
    }

}
