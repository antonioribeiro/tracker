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
 * @author     Antonio Carlos Ribeiro @ PragmaRX
 * @license    BSD License (3-clause)
 * @copyright  (c) 2013, PragmaRX
 * @link       http://pragmarx.com
 */

namespace PragmaRX\Tracker\Data\Repositories;

class Log extends Repository {

	private $currentLogId = null;

	public function updateRoute($route_path_id)
	{
		if ($this->getModel()->id)
		{
			$this->getModel()->route_path_id = $route_path_id;

			$this->getModel()->save();
		}
	}

	public function updateError($error_id)
	{
		if ($this->getModel()->id)
		{
			$this->getModel()->error_id = $error_id;

			$this->getModel()->save();
		}

		return $this->getModel();
	}

	public function bySession($sessionId, $results = true)
	{
		$query = $this
					->getModel()
					->where('session_id', $sessionId)->orderBy('updated_at', 'desc');

		if ($results)
		{
			return $query->get();
		}

		return $query;
	}

	/**
	 * @return null
	 */
	public function getCurrentLogId()
	{
		return $this->currentLogId;
	}

	/**
	 * @param null $currentLogId
	 */
	public function setCurrentLogId($currentLogId)
	{
		$this->currentLogId = $currentLogId;
	}

	public function createLog($data)
	{
		$log = $this->create($data);

		$this->setCurrentLogId($log->id);

		return $this->getCurrentLogId();
	}

	public function pageViews($minutes, $results)
	{
		 return $this->getModel()->pageViews($minutes, $results);
	}

	public function pageViewsByCountry($minutes, $results)
	{
		 return $this->getModel()->pageViewsByCountry($minutes, $results);
	}

	public function getErrors($minutes, $results)
	{
		return $this->getModel()->errors($minutes, $results);
	}

	public function allByRouteName($name, $minutes = null)
	{
		return $this->getModel()->allByRouteName($name, $minutes);
	}

	public function delete()
	{
		$this->getModel()->delete();
	}

}
