<?php

namespace PragmaRX\Tracker\Support;

use UAParser\Parser;
use UAParser\Result\Client;
use UAParser\Result\Device;
use UAParser\Result\OperatingSystem;
use UAParser\Result\UserAgent;

class UserAgentParser
{
    public Client $parser;

    public UserAgent $userAgent;

    public OperatingSystem $operatingSystem;

    public Device $device;

    public string $originalUserAgent;

    public string $basePath;

    public function __construct(string $basePath, string $userAgent = '')
    {
        $this->parser = Parser::create()->parse($this->getUserAgent($userAgent));

        $this->userAgent = $this->parser->ua;

        $this->operatingSystem = $this->parser->os;

        $this->device = $this->parser->device;

        $this->basePath = $basePath;

        $this->originalUserAgent = $this->parser->originalUserAgent;
    }

    public function getOperatingSystemVersion(): string
    {
        return $this->operatingSystem->major .
            ($this->operatingSystem->minor !== null ? '.' . $this->operatingSystem->minor : '') .
            ($this->operatingSystem->patch !== null ? '.' . $this->operatingSystem->patch : '');
    }

    protected function getUserAgent(string $userAgent)
    {
        if (!empty($userAgent)) {
            return $userAgent;
        }

        if (isset($_SERVER['HTTP_USER_AGENT']) && !empty($_SERVER['HTTP_USER_AGENT'])) {
            return $_SERVER['HTTP_USER_AGENT'];
        }

        return config('tracker.default_user_agent', '');
    }

    public function getUserAgentVersion(): string
    {
        return $this->userAgent->major .
            ($this->userAgent->minor !== null ? '.' . $this->userAgent->minor : '') .
            ($this->userAgent->patch !== null ? '.' . $this->userAgent->patch : '');
    }
}
