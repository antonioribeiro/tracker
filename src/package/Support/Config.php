<?php

namespace PragmaRX\Tracker\Package\Support;

use PragmaRX\Tracker\Package\Exceptions\MethodNotFound;
use PragmaRX\Tracker\Package\Exceptions\PropertyNotFound;

class Config
{
    /**
     * Dynamically get config data.
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
        if (!$this->hasVariable($name)) {
            throw new MethodNotFound("Method '{$name}' doesn't exists in this object.");
        }

        return $this->get($name);
    }

    /**
     * Generic getter.
     *
     * @param $name
     *
     * @throws PropertyNotFound
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (!$this->hasVariable($name)) {
            throw new PropertyNotFound("Method '{$name}' doesn't exists in this object.");
        }

        return $this->get($name);
    }

    /**
     * Get config value.
     *
     * @param $var
     * @param mixed|null $default
     *
     * @return \Illuminate\Config\Repository|mixed
     */
    public function get($var, $default = null)
    {
        $var = snake($var);

        return coollect(config('tracker'))->$var;
    }

    /**
     * Get config root.
     *
     * @return \Illuminate\Config\Repository|mixed
     *
     * @internal param $string
     */
    public function getRoot()
    {
        return coollect(config('tracker'));
    }

    /**
     * Check if config has a variable set.
     *
     * @param $name
     *
     * @return bool
     */
    public function hasVariable($name)
    {
        return $this->getRoot()->has($name) ||
            $this->getRoot()->has(snake($name));
    }

    /**
     * Update the config file.
     *
     * @param $config
     */
    public function update($config)
    {
        config(['tracker' => $config]);
    }

    /**
     * Update the config file.
     *
     * @param $config
     */
    public function set($config)
    {
        config(
            collect($config)->mapWithKeys(function ($value, $key) {
                return ["tracker.{$key}" => $value];
            })->toArray()
        );
    }
}
