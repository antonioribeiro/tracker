<?php

namespace PragmaRX\Tracker\Support;


use Carbon\Carbon;

class Minutes {

	private $minutes;

	private $start;

	private $end;

	public function __construct($minutes)
	{
	    $this->minutes = $minutes;

		if ($minutes instanceof Minutes)
		{
			$this->start = $minutes->getStart();

			$this->end = $minutes->getEnd();
		}
		else
		{
			$this->calculateStartEnd();
		}
	}

	public function getStart()
	{
		return $this->start;
	}

	public function getEnd()
	{
		return $this->end;
	}

	private function calculateStartEnd()
	{
		if($this->minutes == 0)
		{
			$this->setToday();
		}
		else
		{
			$this->start = Carbon::now()->subMinutes($this->minutes);

			$this->end = Carbon::now();
		}
	}

	private function setToday()
	{
		$this->start = Carbon::now()->setTime(0,0,0);

		$this->end = Carbon::now()->setTime(23,59,59);
	}

	public static function make($minutes)
	{
		return new static($minutes);
	}
}
