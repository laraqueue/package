<?php

namespace Laraqueue\Traits;

use Carbon\Carbon;
use Laraqueue\Support\Sender;
use Laraqueue\Events\JobReserved;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

/**
 * Class InteractsWithLaraqueue
 *
 * @package Laraqueue\Traits
 */
trait InteractsWithLaraqueue
{

    /**
     * Gets request data.
     *
     * @return array
     */
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

    /**
     * Gets server data.
     *
     * @return array
     */
    protected function getServer()
    {
        return [
            'argv' => gethostname(),
            'host' => array_get($_SERVER, 'argv'),
            'php_version' => phpversion(),
            'root' => base_path(),
        ];
    }

    /**
     * Creates payload from event.
     *
     * @param mixed $event
     * @return array
     */
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
                'raw' => $this->cleanJobData(json_decode($job->getRawBody())->data->command),
            ],
            'laravel_version' => app()->version(),
            'queue' => $job->getQueue(),
            'request' => $this->getRequest(),
            'server' => $this->getServer(),
            'timestamp' => Carbon::now('UTC')->toDateTimeString(),
            'key' => $this->getKey(),
            'user' => Auth::user(),
        ];

        if($exception) {
            $payload['exception'] = [
                'name' => get_class($exception),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'message' => $exception->getMessage(),
            ];
        }

        return $payload;
    }

    /**
     * Creates payload from job.
     *
     * @param mixed $job
     * @return array
     */
    protected function createPayloadFromJob($job)
    {
        $connectionName = $this->getConnection($job);
        $queue = $this->getQueue($job);

        $payload = [
            'attempts' => $job->attempts(),
            'connection' => [
                'config' => $this->getConnectionByName($connectionName),
                'name' => $connectionName,
            ],
            'environment' => App::environment(),
            'event' => JobReserved::class,
            'job' => [
                'id' => $job->id,
                'name' => get_class($job),
                'raw' => $this->cleanJobData(serialize($job)),
            ],
            'laravel_version' => app()->version(),
            'queue' => $queue,
            'request' => $this->getRequest(),
            'server' => $this->getServer(),
            'timestamp' => Carbon::now('UTC')->toDateTimeString(),
            'key' => $this->getKey(),
            'user' => Auth::user(),
        ];

        return $payload;
    }


    /**
     * Recursively removes all attributes defined as `hidden` in config/laraqueue.php.
     *
     * @param mixed $job
     * @return array
     */
    function cleanJobData($job)
    {
        $data = (array) unserialize($job);

        collect(config('laraqueue.hidden', []))->each(function($key) use (&$data) {
            array_recursive_unset($data, $key);
        });

        return $data;
    }

    /**
     * Gets job connection.
     *
     * @param mixed $job
     * @return array
     */
    protected function getConnection($job)
    {
        $connection = method_exists($job, 'getConnectionName')
            ? $job->getConnectionName()
            : $job->connection;

        return $connection ?: $this->getDefaultConnection();
    }

    /**
     * Gets connection by name.
     *
     * @param string $name
     * @return array
     */
    public function getConnectionByName($name)
    {
        return config("queue.connections.{$name}");
    }

    /**
     * Gets default connection.
     *
     * @return array mixed
     */
    public function getDefaultConnection()
    {
        return config('queue.default');
    }

    /**
     * Gets Laraqueue app key.
     *
     * @return string
     */
    public function getKey()
    {
        return config('laraqueue.key');
    }

    /**
     * Gets job queue.
     *
     * @param mixed $job
     * @return string
     */
    protected function getQueue($job)
    {
        return array_get($this->getQueueConfig($job), 'queue', 'default');
    }

    /**
     * Gets job queue config.
     *
     * @param mixed $job
     * @return array
     */
    protected function getQueueConfig($job)
    {
        return $this->getConnectionByName($this->getConnection($job));
    }

}
