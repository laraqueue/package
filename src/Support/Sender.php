<?php

namespace Laraqueue\Support;

use Laraqueue\Traits\InteractsWithLaraqueue;

class Sender
{

    use InteractsWithLaraqueue;

    /**
     * @var string
     */
    protected $api = 'https://dev.laraqueue.com/api/jobs';

    /**
     * @var Client
     */
    protected $client;

    /**
     * Sender constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Posts.
     *
     * @param array $payload
     */
    public function post(array $payload)
    {
        $this->client->post($this->api, $payload);
    }

    /**
     * Sends event.
     *
     * @param mixed $event
     */
    public function sendEvent($event)
    {
        $this->post(
            $this->createPayloadFromEvent($event)
        );
    }

    /**
     * Sends job.
     *
     * @param mixed $job
     */
    public function sendJob($job)
    {
        $this->post(
            $this->createPayloadFromJob($job)
        );
    }

}
