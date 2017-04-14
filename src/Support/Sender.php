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
     * Sends event.
     *
     * @param mixed $event
     */
    public function sendEvent($event)
    {
        if($this->isLaraqueueJobEvent($event)) {
            return;
        }

        $this->post(
            $this->createPayloadFromEvent($event)
        );
    }

    /**
     * Sends job.
     *
     * @param mixed $id
     * @param mixed $job
     */
    public function sendJob($id, $job)
    {
        $this->post(
            $this->createPayloadFromJob($id, $job)
        );
    }

}
