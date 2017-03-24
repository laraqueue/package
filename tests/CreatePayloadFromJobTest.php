<?php

namespace Laraqueue\Tests;

use Academe\SerializeParser\Parser;
use Laraqueue\Traits\InteractsWithLaraqueue;

class CreatePayloadFromJobTest extends LaraqueueTestCase
{

    use InteractsWithLaraqueue;

    protected $jobMock;

    protected $parser;

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $this->jobMock = new JobMock;
        $this->parser = new Parser;

        $app['config']->set('laraqueue.hidden', [
            'foo'
        ]);
    }

    function test_it_removes_sensitive_data()
    {
        $clean = [
            'attributes' => [
                'bar' => 'foo',
                'baz' => [
                    'bar' => 'foo',
                    'baz' => [
                        'bar' => 'foo',
                        'baz' => [
                            //
                        ]
                    ]
                ]
            ],
            '__class_name' => 'Laraqueue\Tests\JobMock'
        ];

        $this->assertEquals($clean, $this->cleanJobData(serialize($this->jobMock)));
    }

}

class JobMock
{

    protected $attributes = [
        'foo' => 'bar',
        'bar' => 'foo',
        'baz' => [
            'foo' => 'bar',
            'bar' => 'foo',
            'baz' => [
                'foo' => 'bar',
                'bar' => 'foo',
                'baz' => [
                    'foo' => 'bar',
                ]
            ]
        ]
    ];

}

