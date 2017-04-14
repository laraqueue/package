<?php

namespace Laraqueue\Jobs;

use Illuminate\Bus\Queueable;
use Laraqueue\Facades\Laraqueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Class ReportToLaraqueue
 *
 * @package Laraqueue\Jobs
 */
class ReportToLaraqueue implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array
     */
    protected $payload;

    /**
     * ReportToLaraqueue constructor.
     *
     * @param array $payload
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    /**
     * Handles job.
     */
    public function handle()
    {
        Laraqueue::post($this->payload);
    }
}
