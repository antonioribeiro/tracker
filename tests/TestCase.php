<?php

namespace PragmaRX\Tracker\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use PragmaRX\Tracker\Package\ServiceProvider as TrackerServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            TrackerServiceProvider::class,
        ];
    }
}
