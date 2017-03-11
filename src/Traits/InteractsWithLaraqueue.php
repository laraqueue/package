<?php

namespace Laraqueue\Traits;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Laraqueue\Events\JobReserved;

trait InteractsWithLaraqueue
{

    protected function getRequest()
    {
        return [
            'header' => request()->header(),
            'ips' => request()->ips(),
            'method' => request()->method(),
            'payload' => request()->all(),
            'url' => request()->fullUrl(),
        ];
    }

    protected function getServer()
    {
        return [
            'argv' => gethostname(),
            'host' => array_get($_SERVER, 'argv'),
            'root' => base_path(),
        ];
    }

    protected function createPayloadFromEvent($event)
    {
        $job = $event->job;
        $exception = object_get($event, 'exception');
        $connectionName = $job->getConnectionName();

        $payload = [
            'attempts' => $job->attempts(),
            'connection' => [
                'name' => $connectionName,
                'config' => $this->getConnectionByName($connectionName)
            ],
            'environment' => App::environment(),
            'event' => get_class($event),
            'job' => [
                'id' => $job->getJobId(),
                'name' => $job->resolveName(),
                'raw' => json_encode($job),
            ],
            'queue' => $job->getQueue(),
            'request' => $this->getRequest(),
            'server' => $this->getServer(),
            'timestamp' => Carbon::now('UTC')->toDateTimeString(),
            'token' => env('LARAQUEUE_TOKEN'),
            'user' => Auth::user(),
        ];

        if($exception) {
            $payload['exception'] = [
                'name' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'message' => $exception->getMessage(),
                'trace' => $exception->getTrace(),
            ];
        }

        return $payload;
    }

    protected function createPayloadFromJob($job)
    {
        $connectionName = $this->getConnection($job);
        $queue = $this->getQueue($job);

        $payload = [
            'connection' => [
                'config' => $this->getConnectionByName($connectionName),
                'name' => $connectionName,
            ],
            'environment' => App::environment(),
            'event' => JobReserved::class,
            'job' => [
                'id' => $job->id,
                'name' => get_class($job),
                'raw' => json_encode($job),
            ],
            'queue' => $queue,
            'request' => $this->getRequest(),
            'server' => $this->getServer(),
            'timestamp' => Carbon::now('UTC')->toDateTimeString(),
            'token' => env('LARAQUEUE_TOKEN'),
            'user' => Auth::user(),
        ];

        return $payload;
    }

    protected function getConnection($job)
    {
        $connection = method_exists($job, 'getConnectionName')
            ? $job->getConnectionName()
            : $job->connection;

        return $connection ?: $this->getDefaultConnection();
    }

    public function getConnectionByName($name)
    {
        return config("queue.connections.{$name}");
    }

    public function getDefaultConnection()
    {
        return config('queue.default');
    }

    protected function getQueue($job)
    {
        return array_get($this->getQueueConfig($job), 'queue', 'default');
    }

    protected function getQueueConfig($job)
    {
        return $this->getConnectionByName($this->getConnection($job));
    }

    public function getToken()
    {
        return config('services.laraqueue.token');
    }

    protected function isSync($job)
    {
        return array_get($this->getQueueConfig($job), 'driver') === 'sync';
    }

}
