<?php

namespace PragmaRX\Tracker\Vendor\Laravel\Models;

class Connection extends Base
{
    protected $table = 'tracker_connections';

    protected $fillable = [
        'name',
    ];
}
