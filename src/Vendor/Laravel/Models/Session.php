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

class Session extends Base {

	protected $table = 'tracker_sessions';

	protected $fillable = array(
		'uuid',
		'user_id',
		'device_id',
		'agent_id',
		'client_ip',
		'cookie_id',
		'referer_id',
		'geoip_id',
		'is_robot',
	);

	public function __construct(array $attributes = array())
	{
		parent::__construct($attributes);
	}

	public function user()
	{
		return $this->belongsTo($this->getConfig()->get('user_model'));
	}

	public function device()
	{
		return $this->belongsTo($this->getConfig()->get('device_model'));
	}

	public function agent()
	{
		return $this->belongsTo($this->getConfig()->get('agent_model'));
	}

	public function referer()
	{
		return $this->belongsTo($this->getConfig()->get('referer_model'));
	}

	public function geoIp()
	{
		return $this->belongsTo($this->getConfig()->get('geoip_model'), 'geoip_id');
	}

	public function log()
	{
		return $this->hasMany($this->getConfig()->get('log_model'));
	}

	public function getPageViewsAttribute()
	{
		return $this->log()->count();
	}

	public function users($minutes)
	{
        $hour = Carbon::now()->subMinutes($minutes ?: 60 * 24);

		return
			$this
				->select(
					'user_id',
					$this->getConnection()->raw('max(updated_at) as updated_at')
				)
				->groupBy('user_id')
				->from('tracker_sessions')
				->where('updated_at', '>=', $hour)
				->whereNotNull('user_id')
				->orderBy($this->getConnection()->raw('max(updated_at)'), 'desc')
				->get();
	}
}
