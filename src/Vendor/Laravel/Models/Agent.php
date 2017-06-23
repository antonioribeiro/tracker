<?php

namespace PragmaRX\Tracker\Vendor\Laravel\Models;

class Agent extends Base
{
    protected $table = 'tracker_agents';

    protected $fillable = ['name',
    							'hash',
                                'browser',
                                'browser_version', ];
}
