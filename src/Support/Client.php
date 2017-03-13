<?php

namespace Laraqueue\Support;

use GuzzleHttp\Exception\RequestException;

/**
 * Class Client
 *
 * @package Laraqueue\Support
 */
class Client
{

    /**
     * @var \GuzzleHttp\Client
     */
    protected $guzzle;

    /**
     * Client constructor.
     *
     * @param string $token
     */
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

    /**
     * Posts.
     *
     * @param string $uri
     * @param array $payload
     */
    public function post($uri, array $payload)
    {
        try {
            $this->guzzle->post($uri, ['body' => json_encode($payload)]);
        } catch (RequestException $e) {
            //
        }
    }

}
