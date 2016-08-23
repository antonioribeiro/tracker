<?php

namespace PragmaRX\Tracker\Support;

use Jenssegers\Agent\Agent;

class MobileDetect extends Agent
{
    /**
     * Detect kind, model and mobility.
     *
     * @return array
     */
    public function detectDevice()
    {
        return [
                    'kind'      => $this->getDeviceKind(),
                    'model'     => $this->device(),
                    'is_mobile' => $this->isMobile(),
                    'is_robot'  => $this->isRobot(),
                ];
    }

    /**
     * Get the kind of device.
     *
     * @internal param $mobile
     *
     * @return string
     */
    public function getDeviceKind()
    {
        $kind = 'unavailable';

        if ($this->isTablet()) {
            $kind = 'Tablet';
        } elseif ($this->isPhone()) {
            $kind = 'Phone';
        } elseif ($this->isComputer()) {
            $kind = 'Computer';
        }

        return $kind;
    }

    /**
     * Is this a phone?
     *
     * @return bool
     */
    public function isPhone($userAgent = null, $httpHeaders = null)
    {
        return !$this->isTablet() && !$this->isComputer();
    }

    /**
     * Is this a computer?
     *
     * @return bool
     */
    public function isComputer()
    {
        return !$this->isMobile();
    }
}
