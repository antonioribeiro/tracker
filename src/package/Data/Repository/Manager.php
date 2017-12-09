<?php

namespace PragmaRX\Tracker\Data\Repositories;

class Manager
{
    /**
     * Check if a user agent parser is available.
     *
     * @return bool
     */
    public function userAgentParserIsAvailable()
    {
        return $this->userAgent->parserIsAvailable();
    }
}
