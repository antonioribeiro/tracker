<?php

namespace PragmaRX\Tracker\Tests;

use Illuminate\Support\Collection;
use PragmaRX\Tracker\Package\Facade as TrackerFacade;
use PragmaRX\Tracker\Package\Tracker as TrackerService;

class TrackerTest extends TestCase
{
    /**
     * @var TrackerService
     */
    private $tracker;

    public function setUp()
    {
        parent::setup();

        $this->tracker = TrackerFacade::instance();
    }

    public function test_can_instantiate_service()
    {
        $this->assertInstanceOf(TrackerService::class, $this->tracker);
    }
}
