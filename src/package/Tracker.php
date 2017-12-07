<?php

namespace PragmaRX\Tracker\Package;

use PragmaRX\Tracker\Package\Support\Config;
use PragmaRX\Tracker\Package\Support\Logger;
use PragmaRX\Tracker\Package\Exceptions\MethodNotFound;

class Tracker
{
    /**
     * @var bool
     */
    protected $booted = false;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * Version constructor.
     *
     * @param Config $config
     * @param Logger $logger
     */
    public function __construct(Config $config, Logger $logger)
    {
        $this->config = $config;

        $this->logger = $logger;
    }

    /**
     * Dynamically call format types.
     *
     * @param $name
     * @param array $arguments
     *
     * @throws MethodNotFound
     *
     * @return mixed
     */
    public function __call($name, array $arguments)
    {
        foreach (['config'] as $object) {
            if (method_exists($this->{$object}, $name)) {
                return call_user_func_array([$this->{$object}, $name], $arguments);
            }
        }

        throw new MethodNotFound("Method '{$name}' doesn't exists in this object.");
    }

    /**
     * Config getter.
     *
     * @return Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Get this object instance.
     *
     * @return $this
     */
    public function instance()
    {
        return $this;
    }

    /**
     * Boot & track.
     *
     * @return bool
     */
    public function boot()
    {
        if ($this->booted) {
            return false;
        }

        $this->setBooted(true);

        return $this->track();
    }

    /**
     * Check if Tracker is booted.
     *
     * @return bool
     */
    public function isBooted()
    {
        return $this->booted;
    }

    /**
     * Check if the current request is trackable.
     *
     * @return mixed
     */
    protected function isTrackable()
    {
        dd($this->config->enabled, $this->config->logEnabled);
        return $this->config->enabled &&
               $this->config->logEnabled //&&

//            $this->logIsEnabled() &&
//            $this->allowConsole() &&
//            $this->parserIsAvailable() &&
//            $this->isTrackableIp() &&
//            $this->isTrackableEnvironment() &&
//            $this->routeIsTrackable() &&
//            $this->pathIsTrackable() &&
//            $this->notRobotOrTrackable()
        ;
    }

    /**
     * Booted setter.
     *
     * @param bool $booted
     */
    public function setBooted(bool $booted)
    {
        $this->booted = $booted;
    }

    /**
     * Track current request.
     *
     * @return bool
     */
    public function track()
    {
        return !$this->isTrackable()
            ? false
            : $this->logger->log();
    }
}
