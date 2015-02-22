<?php

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
