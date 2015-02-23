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
		if ($GLOBALS["app"] instanceof Application)
		{
			return app()->make('tracker.config');
		}

		return $GLOBALS["app"]["tracker.config"];
	}

	public function scopePeriod($query, $minutes, $alias = '')
	{
		$alias = $alias ? "$alias." : '';

		return $query
				->where($alias.'updated_at', '>=', $minutes->getStart())
				->where($alias.'updated_at', '<=', $minutes->getEnd());
	}

}
