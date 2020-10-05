<?php

namespace PragmaRX\Tracker\Support;

use UAParser\Parser;

class UserAgentParser
{
    public $parser;

    public $userAgent;

    public $operatingSystem;

    public $device;

    public $originalUserAgent;

    public function __construct($basePath, $userAgent = '')
    {
        $this->parser = Parser::create()->parse($this->getUserAgent($userAgent));

        $this->userAgent = $this->parser->ua;

        $this->operatingSystem = $this->parser->os;

        $this->device = $this->parser->device;

        $this->basePath = $basePath;

        $this->originalUserAgent = $this->parser->originalUserAgent;
    }

    public function getOperatingSystemVersion()
    {
        return    $this->operatingSystem->major.
                ($this->operatingSystem->minor !== null ? '.'.$this->operatingSystem->minor : '').
                ($this->operatingSystem->patch !== null ? '.'.$this->operatingSystem->patch : '');
    }

    protected function getUserAgent($userAgent)
    {
        if (!empty($userAgent)) {
            return $userAgent;
        }

        if (isset($_SERVER['HTTP_USER_AGENT']) && !empty($_SERVER['HTTP_USER_AGENT'])) {
            return $_SERVER['HTTP_USER_AGENT'];
        }

        return config('tracker.default_user_agent', '');
    }

    public function getUserAgentVersion()
    {
        return  $this->userAgent->major.
                ($this->userAgent->minor !== null ? '.'.$this->userAgent->minor : '').
                ($this->userAgent->patch !== null ? '.'.$this->userAgent->patch : '');
    }
}
