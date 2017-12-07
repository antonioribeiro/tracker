<?php

namespace PragmaRX\Tracker\Tests;

use PragmaRX\Tracker\Package\Exceptions\MissingConfiguration;
use PragmaRX\Tracker\Package\Tracker as TrackerService;

class TrackerTest extends TestCase
{
    public function setUp()
    {
        parent::setup();

        $this->tracker->boot();
    }

    public function test_can_instantiate_service()
    {
        $this->assertInstanceOf(TrackerService::class, $this->tracker);
    }

    public function test_can_detect_missing_configuration()
    {
        $this->tracker->track();
    }
}
