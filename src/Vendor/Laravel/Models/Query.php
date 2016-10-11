<?php

namespace PragmaRX\Tracker\Vendor\Laravel\Models;

class Query extends Base
{
    protected $table = 'tracker_queries';

    protected $fillable = [
        'query',
    ];

    public function arguments()
    {
        return $this->hasMany($this->getConfig()->get('query_argument_model'));
    }
}
