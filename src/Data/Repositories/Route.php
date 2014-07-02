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

namespace PragmaRX\Tracker\Data\Repositories;

use PragmaRX\Tracker\Support\Config;

class Route extends Repository {

	public function __construct($model, Config $config)
	{
		parent::__construct($model);

		$this->config = $config;
	}

	public function isTrackable($route)
	{
		$forbidden = $this->config->get('do_not_track_routes');

		return
			$route->currentRouteName() &&
			! in_array_wildcard($route->currentRouteName(), $forbidden);
	}

}
