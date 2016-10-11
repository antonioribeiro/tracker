<?php

namespace PragmaRX\Tracker\Vendor\Laravel\Models;

class SqlQueryBindingParameter extends Base
{
    protected $table = 'tracker_sql_query_bindings_parameters';

    protected $fillable = [
        'sql_query_bindings_id',
        'name',
        'value',
    ];
}
