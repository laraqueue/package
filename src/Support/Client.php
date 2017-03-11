<?php

namespace Laraqueue\Support;

class Client
{

    protected $guzzle;

    public function __construct($token)
    {
        $this->guzzle = new \GuzzleHttp\Client([
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => $token,
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    public function post($uri, $payload)
    {
        $this->guzzle->post($uri, $payload);
    }

}
