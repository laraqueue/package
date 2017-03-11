<?php

namespace Laraqueue\Providers;

use Laraqueue\Support\Client;
use Laraqueue\Support\Sender;
use Laraqueue\Support\Connector;
use Laraqueue\Support\Dispatcher;
use Illuminate\Queue\QueueManager;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\ServiceProvider;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Laraqueue\Traits\InteractsWithLaraqueue;
use Illuminate\Queue\Events\JobExceptionOccurred;

class LaraqueueServiceProvider extends ServiceProvider
{

    use InteractsWithLaraqueue;

    protected $manager;

    protected $queue;

    protected $sender;

    public function boot()
    {
        if(!$this->getToken()) {
            return;
        }

        $this->manager = new QueueManager($this->app);
        $this->sender = app(Sender::class);

        $this->registerListeners();
    }

    public function register()
    {
        $this->app->singleton(Client::class, function() {
            return new Client($this->getToken());
        });
        $this->app->singleton(Sender::class);
        $this->app->bind('Laraqueue', Dispatcher::class);
    }

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

    protected function handleEvent($event)
    {
        if($this->isSync($event->job)) {
            return;
        }

        $this->sender->sendEvent($event);
    }

}
