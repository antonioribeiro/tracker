<?php

namespace PragmaRX\Tracker\Package;

use PragmaRX\Tracker\Package\Exceptions\MethodNotFound;
use PragmaRX\Tracker\Package\Support\Config;
use PragmaRX\Yaml\Package\Yaml;

class Tracker
{
    /**
     * Version constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config = null, Yaml $yaml = null)
    {
        $this->instantiate($config, $yaml);
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
     * Instantiate all dependencies.
     *
     * @param $config
     * @param $yaml
     */
    protected function instantiate($config, $yaml)
    {
        $yaml = $this->instantiateClass($yaml, 'yaml', Yaml::class);

        $config = $this->instantiateClass($config, 'config', Config::class, [$yaml]);

//
//        $this->instantiateClass($parser, 'parser', Parser::class);
//
//        $this->instantiateClass($resolver, 'resolver', Resolver::class);
    }

    /**
     * Instantiate a class.
     *
     * @param $instance  object
     * @param $property  string
     * @param $class     string
     * @param array $arguments
     *
     * @return object|Tracker
     */
    protected function instantiateClass($instance, $property, $class = null, $arguments = [])
    {
        return $this->{$property} = is_null($instance)
            ? $instance = new $class(...$arguments)
            : $instance;
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
}
