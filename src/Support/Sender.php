<?php

namespace Laraqueue\Support;

use Laraqueue\Traits\InteractsWithLaraqueue;

class Sender
{

    use InteractsWithLaraqueue;

    protected $api = 'https://dev.laraqueue.com/api/jobs';

    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function post(array $payload)
    {
        $this->client->post($this->api, [
            'body' => json_encode($payload)
        ]);
    }

    public function sendEvent($event)
    {
        $this->post(
            $this->createPayloadFromEvent($event)
        );
    }

    public function sendJob($job)
    {
        $this->post(
            $this->createPayloadFromJob($job)
        );
    }

}
