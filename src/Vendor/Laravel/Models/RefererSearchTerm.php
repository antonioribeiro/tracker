<?php

namespace PragmaRX\Tracker\Vendor\Laravel\Models;

class RefererSearchTerm extends Base
{
    protected $table = 'tracker_referers_search_terms';

    protected $fillable = [
        'referer_id',
        'search_term',
    ];
}
