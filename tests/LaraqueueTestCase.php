<?php

namespace Laraqueue\Tests;

use Orchestra\Testbench\TestCase;

class LaraqueueTestCase extends TestCase
{

    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('laraqueue.hidden', [
            'foo'
        ]);
    }

}
