<?php

namespace Laraqueue\Support;

use Laraqueue\Traits\InteractsWithLaraqueue;

/**
 * Class Sender
 *
 * @package Laraqueue\Support
 */
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
     * Reports event to Laraqueue.
     *
     * @param mixed $event
     */
    public function reportEvent($event)
    {
        $this->post($this->createPayloadFromEvent($event));
    }

    /**
     * Reports job to Laraqueue.
     *
     * @param mixed $job
     */
    public function reportJob($job)
    {
        $this->post($this->createPayloadFromJob($job));
    }

}
