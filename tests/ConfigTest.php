<?php

namespace PragmaRX\Tracker\Tests;

class ConfigTest extends TestCase
{
    public function test_get_config_vars()
    {
        $this->assertTrue($this->tracker->getConfig()->get('enabled'));

        $this->assertTrue($this->tracker->getConfig()->enabled);

        $this->assertTrue($this->tracker->getConfig()->enabled());

        $this->assertTrue($this->tracker->getConfig()->logEnabled());

        $this->assertTrue($this->tracker->getConfig()->log_enabled);
    }
}
