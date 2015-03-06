<?php

namespace PragmaRX\Tracker\Vendor\Laravel\Models;

use Symfony\Component\Console\Application;
use Illuminate\Database\Eloquent\Model as Eloquent;

class Base extends Eloquent {

	public function __construct(array $attributes = array())
	{
		parent::__construct($attributes);

		$this->setConnection($this->getConfig()->get('connection'));
	}

	public function getConfig()
	{
		if (isset($GLOBALS["app"]) && $GLOBALS["app"] instanceof Application)
		{
			return $GLOBALS["app"]["tracker.config"];
		}

		return app()->make('tracker.config');
	}

	public function scopePeriod($query, $minutes, $alias = '')
	{
		$alias = $alias ? "$alias." : '';

		return $query
				->where($alias.'updated_at', '>=', $minutes->getStart())
				->where($alias.'updated_at', '<=', $minutes->getEnd());
	}

}
