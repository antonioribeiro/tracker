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

use Carbon\Carbon;

class Log extends Base {

	protected $table = 'tracker_log';

	protected $fillable = array(
		'session_id',
		'method',
		'path_id',
		'query_id',
		'route_path_id',
		'is_ajax',
		'is_secure',
		'is_json',
		'wants_json',
		'error_id',
	);

	public function session()
	{
		return $this->belongsTo($this->getConfig()->get('session_model'));
	}

	public function path()
	{
		return $this->belongsTo($this->getConfig()->get('path_model'));
	}

	public function error()
	{
		return $this->belongsTo($this->getConfig()->get('error_model'));
	}

	public function logQuery()
	{
		return $this->belongsTo($this->getConfig()->get('query_model'), 'query_id');
	}

	public function routePath()
	{
		return $this->belongsTo($this->getConfig()->get('route_path_model'), 'route_path_id');
	}

	public function pageViews($minutes)
	{
		$hour = Carbon::now()->subMinutes($minutes ?: 60 * 24);

		return $this->select(
				$this->getConnection()->raw('DATE(created_at) as date, count(*) as total')
			)->groupBy(
				$this->getConnection()->raw('DATE(created_at)')
			)
			->where('created_at', '>=', $hour)
			->orderBy('date')
			->get();
	}

	public function pageViewsByCountry($minutes)
	{
		$hour = Carbon::now()->subMinutes($minutes ?: 60 * 24);

		return
			$this
			->select(
				'tracker_geoip.country_name as label'
				, $this->getConnection()->raw('count(tracker_log.id) as value')
			)
			->join('tracker_sessions', 'tracker_log.session_id', '=', 'tracker_sessions.id')
			->join('tracker_geoip', 'tracker_sessions.geoip_id', '=', 'tracker_geoip.id')
			->groupBy('tracker_geoip.country_name')
			->where('tracker_log.created_at', '>=', $hour)
			->whereNotNull('tracker_sessions.geoip_id')
			->get();
	}

	public function errors($minutes)
	{
		$hour = Carbon::now()->subMinutes($minutes ?: 60 * 24);

		return $this
			->where('tracker_log.created_at', '>=', $hour)
			->whereNotNull('error_id')
			->orderBy('created_at', 'desc')
			->get();
	}

	public function allByRouteName($name, $minutes = null)
	{
		$result = $this
					->join('tracker_route_paths', 'tracker_route_paths.id', '=', 'tracker_log.route_path_id')

					->join(
						'tracker_route_path_parameters',
						'tracker_route_path_parameters.route_path_id',
						'=',
						'tracker_route_paths.id'
					)

					->join('tracker_routes', 'tracker_routes.id', '=', 'tracker_route_paths.route_id')

					->where('tracker_routes.name', $name);

		if ($minutes)
		{
			$hour = Carbon::now()->subMinutes($minutes ?: 60 * 24);

			$result->where('tracker_log.created_at', '>=', $hour);
		}

		return $result;
	}
}
