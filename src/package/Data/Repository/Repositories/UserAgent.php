<?php

namespace PragmaRX\Tracker\Data\Repository\Repositories;

class UserAgent
{
    /**
     * Check if parser is available.
     *
     * @return bool
     */
    public function userAgentParserIsAvailable()
    {
        if (!$this->repository->userAgentParserIsAvailable()) {
            $this->error(trans('tracker::tracker.regex_file_not_available'));

            return false;
        }

        return true;
    }
}
