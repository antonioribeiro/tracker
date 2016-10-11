<?php

namespace PragmaRX\Tracker\Vendor\Laravel\Models;

class SystemClass extends Base
{
    protected $table = 'tracker_system_classes';

    protected $fillable = [
        'name',
    ];
}
