<?php

namespace Opscale\NovaToolbarActions\Tests;

use Opscale\NovaToolbarActions\ToolServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            ToolServiceProvider::class,
        ];
    }
}
