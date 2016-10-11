<?php

namespace PragmaRX\Tracker\Vendor\Laravel\Models;

class RoutePathParameter extends Base
{
    protected $table = 'tracker_route_path_parameters';

    protected $fillable = [
        'route_path_id',
        'parameter',
        'value',
    ];
}
