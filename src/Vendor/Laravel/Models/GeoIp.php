<?php

/**
 * Part of the Tracker package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.  It is also available at
 * the following URL: http://www.opensource.org/licenses/BSD-3-Clause
 *
 * @package    Tracker
 * @version    1.0.0
 * @author     Antonio Carlos Ribeiro @ PragmaRX
 * @license    BSD License (3-clause)
 * @copyright  (c) 2013, PragmaRX
 * @link       http://pragmarx.com
 */

namespace PragmaRX\Tracker\Vendor\Laravel\Models;

class GeoIp extends Base {

	protected $table = 'tracker_geoip';

	protected $fillable = array(
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
	);

}
