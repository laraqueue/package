<?php

namespace Laraqueue\Providers;

use Laraqueue\Bus\Dispatcher;
use Laraqueue\Support\Client;
use Laraqueue\Support\Sender;
use Illuminate\Support\Facades\Queue;
use Illuminate\Bus\BusServiceProvider;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Laraqueue\Traits\InteractsWithLaraqueue;
use Illuminate\Queue\Events\JobExceptionOccurred;
use Illuminate\Bus\Dispatcher as IlluminateDispatcher;
use Illuminate\Contracts\Bus\Dispatcher as DispatcherContract;
use Illuminate\Contracts\Queue\Factory as QueueFactoryContract;
use Illuminate\Contracts\Bus\QueueingDispatcher as QueueingDispatcherContract;

/**
 * Class LaraqueueServiceProvider
 *
 * @package Laraqueue\Providers
 */
class LaraqueueServiceProvider extends BusServiceProvider
{

    use InteractsWithLaraqueue;

    /**
     * Boots service provider.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../assets/config/laraqueue.php' => config_path('laraqueue.php'),
        ], 'config');

        if($this->shouldBoot()) {
            $this->registerListeners();
        }
    }

    /**
     * Registers service provider.
     */
    public function register()
    {
        parent::register();

        if($this->shouldRegister()) {
            $this->registersLaraqueueProviders();
            $this->registerOverrides();
        }
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
     * Registers BusServiceProvider overrides.
     */
    protected function registerOverrides()
    {
        $this->app->singleton(IlluminateDispatcher::class, function ($app) {
            return new Dispatcher($app, function ($connection = null) use ($app) {
                return $app[QueueFactoryContract::class]->connection($connection);
            });
        });

        $this->app->alias(
            IlluminateDispatcher::class, DispatcherContract::class
        );

        $this->app->alias(
            IlluminateDispatcher::class, QueueingDispatcherContract::class
        );
    }

    /**
     * Registers Laraqueue service providers.
     */
    protected function registersLaraqueueProviders()
    {
        $this->app->singleton(Client::class, function() {
            return new Client($this->getKey());
        });
        $this->app->singleton(Sender::class);
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

        app(Sender::class)->reportEvent($event);
    }

    /**
     * Validates job is synchronous.
     *
     * @param mixed $job
     * @return bool
     */
    protected function isSync($job)
    {
        return array_get($this->getQueueConfig($job), 'driver') === 'sync';
    }

    /**
     * Validates should boot service provider.
     *
     * @return bool
     */
    protected function shouldBoot()
    {
        return (bool) $this->getKey();
    }

    /**
     * Validates should register service provider.
     *
     * @return bool
     */
    protected function shouldRegister()
    {
        return (bool) $this->getKey();
    }

}
