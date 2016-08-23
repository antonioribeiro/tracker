<?php

namespace PragmaRX\Tracker\Vendor\Laravel\Models;

class QueryArgument extends Base
{
    protected $table = 'tracker_query_arguments';

    protected $fillable = [
        'query_id',
        'argument',
        'value',
    ];
}
