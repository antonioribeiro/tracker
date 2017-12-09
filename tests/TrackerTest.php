<?php

namespace PragmaRX\Tracker\Tests;

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

    public function test_can_load_configuration()
    {
        $this->assertTrue($this->tracker->getConfig()->enabled);
    }

    public function test_can_work_on_console()
    {
        $this->assertTrue($this->tracker->allowConsole());

        $this->tracker->getConfig()->set(['console_log_enabled' => false]);

        $this->assertFalse($this->tracker->allowConsole());
    }
}
