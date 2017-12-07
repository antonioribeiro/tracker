<?php

namespace PragmaRX\Tracker\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use PragmaRX\Tracker\Package\Facade as TrackerFacade;
use PragmaRX\Tracker\Package\ServiceProvider as TrackerServiceProvider;
use PragmaRX\Tracker\Package\Tracker as TrackerService;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * @var TrackerService
     */
    protected $tracker;

    private function copyConfig()
    {
        copy(
            __DIR__.'/../src/config/tracker.php',
            __DIR__.'/../vendor/orchestra/testbench-core/laravel/config/tracker.php'
        );
    }

    public function setUp()
    {
        $this->copyConfig();

        parent::setup();

        $this->tracker = TrackerFacade::instance();
    }

    protected function getPackageProviders($app)
    {
        return [
            TrackerServiceProvider::class,
        ];
    }
}
