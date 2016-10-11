<?php

namespace PragmaRX\Tracker\Vendor\Laravel\Models;

class GeoIp extends Base
{
    protected $table = 'tracker_geoip';

    protected $fillable = [
        'country_code',
        'country_code3',
        'country_name',
        'region',
        'city',
        'postal_code',
        'latitude',
        'longitude',
        'area_code',
        'dma_code',
        'metro_code',
        'continent_code',
    ];
}
