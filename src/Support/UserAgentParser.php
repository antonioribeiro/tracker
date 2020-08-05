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
    	$defaultUserAgent = 'Mozilla/5.0 (Windows NT 5.1; rv:31.0) Gecko/20100101 Firefox/31.0';
        if (!$userAgent) {
            $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : $defaultUserAgent;
        }

        $this->parser = Parser::create()->parse($userAgent);

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

    public function getUserAgentVersion()
    {
        return  $this->userAgent->major.
                ($this->userAgent->minor !== null ? '.'.$this->userAgent->minor : '').
                ($this->userAgent->patch !== null ? '.'.$this->userAgent->patch : '');
    }
}
