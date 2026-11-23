<?php

namespace Said\ChangeRequests\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Said\ChangeRequests\ChangeRequestsServiceProvider;

abstract class TestCase extends Orchestra
{
    /**
     * @return list<class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [ChangeRequestsServiceProvider::class];
    }
}
