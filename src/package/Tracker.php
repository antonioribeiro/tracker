<?php

namespace PragmaRX\Tracker\Package;

use PragmaRX\Tracker\Package\Exceptions\MethodNotFound;

class Tracker
{
    /**
     * Version constructor.
     */
    public function __construct()
    {
        $this->instantiate();
    }

    /**
     * Instantiate all dependencies.
     */
    protected function instantiate()
    {
//        $this->instantiateClass($file, 'file', File::class);
//
//        $this->instantiateClass($parser, 'parser', Parser::class);
//
//        $this->instantiateClass($resolver, 'resolver', Resolver::class);
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
        foreach (['file', 'parser'] as $object) {
            if (method_exists($this->{$object}, $name)) {
                return call_user_func_array([$this->{$object}, $name], $arguments);
            }
        }

        throw new MethodNotFound("Method '{$name}' doesn't exists in this object.");
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
