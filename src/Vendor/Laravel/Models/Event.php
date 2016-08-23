<?php

namespace PragmaRX\Tracker\Vendor\Laravel\Models;

class Event extends Base
{
    protected $table = 'tracker_events';

    protected $fillable = [
        'name',
    ];

    public function allInThePeriod($minutes, $result)
    {
        $query =
            $this
                ->select(
                    'tracker_events.id',
                    'tracker_events.name',
                    $this->getConnection()->raw('count('.$this->getEventLogTableName().'.id) as total')
                )
                ->from('tracker_events')
                ->period($minutes, 'tracker_events_log')
                ->join('tracker_events_log', 'tracker_events_log.event_id', '=', 'tracker_events.id')
                ->groupBy('tracker_events.id', 'tracker_events.name')
                ->orderBy('total', 'desc');

        if ($result) {
            return $query->get();
        }

        return $query;
    }

    private function getEventLogTableName()
    {
        return $this->getTablePrefix().'tracker_events_log';
    }

    /**
     * @return string
     */
    private function getTablePrefix()
    {
        return $this->getConnection()->getTablePrefix();
    }
}
