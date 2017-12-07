<?php

namespace PragmaRX\Tracker\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use PragmaRX\Tracker\Package\ServiceProvider as TrackerServiceProvider;
use PragmaRX\Tracker\Package\Facade as TrackerFacade;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * @var TrackerService
     */
    protected $tracker;

    public function setUp()
    {
        parent::setup();

        \Artisan::call('vendor:publish', ['--provider' => 'PragmaRX\\Tracker\\Vendor\\Laravel\\ServiceProvider']);

        $this->tracker = TrackerFacade::instance();
    }

    protected function getPackageProviders($app)
    {
        return [
            TrackerServiceProvider::class,
        ];
    }
}
