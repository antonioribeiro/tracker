<?php

namespace PragmaRX\Tracker\Vendor\Laravel\Models;

class Route extends Base
{
    protected $table = 'tracker_routes';

    protected $fillable = [
        'name',
        'action',
    ];

    public function paths()
    {
        return $this->hasMany($this->getConfig()->get('route_path_model'));
    }
}
