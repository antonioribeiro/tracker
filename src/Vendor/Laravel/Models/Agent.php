<?php

namespace PragmaRX\Tracker\Vendor\Laravel\Models;

class Agent extends Base
{
    protected $table = 'tracker_agents';

    protected $fillable = [
        'name',
        'browser',
        'browser_version',
        'name_hash',
    ];
}
