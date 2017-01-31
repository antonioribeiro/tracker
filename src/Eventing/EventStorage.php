<?php
/**
 * Created by PhpStorm.
 * User: AntonioCarlos
 * Date: 03/06/2014
 * Time: 18:30.
 */

namespace PragmaRX\Tracker\Eventing;

class EventStorage
{
    private $events = [];

    private $isOn = true;

    public function logEvent($event, $object)
    {
        $this->events[] = [
            'event'  => $event,
            'object' => $object,
        ];
    }

    public function popAll()
    {
        $events = $this->events;

        $this->events = [];

        return $events;
    }

    public function turnOff()
    {
        $this->isOn = false;
    }

    public function turnOn()
    {
        $this->isOn = true;
    }

    public function isOn()
    {
        return $this->isOn;
    }

    public function isOff()
    {
        return !$this->isOn;
    }
}
