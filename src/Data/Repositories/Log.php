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

class Log extends Repository {

	public function updateRoute($route_path_id)
	{
		if ($this->model->id)
		{
			$this->model->route_path_id = $route_path_id;

			$this->model->save();
		}
	}

	public function updateError($error_id)
	{
		if ($this->model->id)
		{
			$this->model->error_id = $error_id;

			$this->model->save();
		}
	}

	public function bySession($sessionId)
	{
		return $this->model->where('session_id', $sessionId)->orderBy('updated_at', 'desc')->get();
	}

	public function pageViews()
	{
		return $this->model->select(
			$this->model->raw('DATE(created_at) as date, count(*) as total')
		)->get();

		// where('session_id', $sessionId)->orderBy('updated_at', 'desc')->get();

		// select  from tracker_log group by DATE(created_at);
	}
}
