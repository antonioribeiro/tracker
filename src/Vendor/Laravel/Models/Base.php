<?php

namespace PragmaRX\Tracker\Vendor\Laravel\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
use Symfony\Component\Console\Application;

class Base extends Eloquent
{
    protected $hidden = ['config'];

    private $config;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setConnection($this->getConfig()->get('connection'));
    }

    public function getConfig()
    {
        if ($this->config) {
            return $this->config;
        } elseif (isset($GLOBALS['app']) && $GLOBALS['app'] instanceof Application) {
            return $GLOBALS['app']['tracker.config'];
        }

        return app()->make('tracker.config');
    }

    public function save(array $options = [])
    {
        parent::save($options);

        app('tracker.cache')->makeKeyAndPut($this, $this->getKeyName());
    }

    public function setConfig($config)
    {
        $this->config = $config;
    }

    public function scopePeriod($query, $minutes, $alias = '')
    {
        $alias = $alias ? "$alias." : '';

        return $query
            ->where($alias.'updated_at', '>=', $minutes->getStart() ? $minutes->getStart() : 1)
            ->where($alias.'updated_at', '<=', $minutes->getEnd() ? $minutes->getEnd() : 1);
    }
}
