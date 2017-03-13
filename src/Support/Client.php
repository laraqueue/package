<?php

namespace Laraqueue\Support;

use GuzzleHttp\Exception\RequestException;

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
        try {
            $this->guzzle->post($uri, ['body' => json_encode($payload)]);
        } catch (RequestException $e) {
            //
        }
    }

}
