<?php

namespace PragmaRX\Tracker\Vendor\Laravel\Support;

use Illuminate\Support\Facades\Request;
use PragmaRX\Tracker\Support\Minutes;
use Session as LaravelSession;

class Session
{
    private $minutes;

    public function __construct()
    {
        LaravelSession::put('tracker.stats.days', $this->getValue('days', 1));

        LaravelSession::put('tracker.stats.page', $this->getValue('page', 'visits'));

        $this->minutes = new Minutes(60 * 24 * LaravelSession::get('tracker.stats.days'));
    }

    /**
     * @return Minutes
     */
    public function getMinutes()
    {
        return $this->minutes;
    }

    public function getValue($variable, $default = null)
    {
        if (Request::has($variable)) {
            $value = Request::get($variable);
        } else {
            $value = LaravelSession::get('tracker.stats.'.$variable, $default);
        }

        return $value;
    }
}
