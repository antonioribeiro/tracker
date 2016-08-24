<?php

namespace PragmaRX\Tracker\Support;

use Carbon\Carbon;

class Minutes
{
    /**
     * @var
     */
    private $minutes;

    /**
     * @var
     */
    private $start;

    /**
     * @var
     */
    private $end;

    /**
     * Minutes constructor.
     *
     * @param $minutes
     */
    public function __construct($minutes = null)
    {
        if (!isset($minutes)) {
            return;
        }

        $this->minutes = $minutes;

        if ($minutes instanceof self) {
            $this->start = $minutes->getStart();

            $this->end = $minutes->getEnd();
        } else {
            $this->calculateStartEnd();
        }
    }

    /**
     * Calculate start and end dates.
     */
    private function calculateStartEnd()
    {
        if ($this->minutes == 0) {
            $this->setToday();
        } else {
            $this->start = Carbon::now()->subMinutes($this->minutes);

            $this->end = Carbon::now();
        }
    }

    /**
     * @return mixed
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @return mixed
     */
    public function getMinutes()
    {
        return $this->minutes;
    }

    /**
     * @return mixed
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @param $minutes
     *
     * @return static
     */
    public static function make($minutes)
    {
        return new static($minutes);
    }

    /**
     * @param mixed $end
     */
    public function setEnd($end)
    {
        $this->end = $end;
    }

    /**
     * @param mixed $start
     */
    public function setStart($start)
    {
        $this->start = $start;
    }

    /**
     * Today.
     */
    private function setToday()
    {
        $this->start = Carbon::now()->setTime(0, 0, 0);

        $this->end = Carbon::now()->setTime(23, 59, 59);
    }
}
