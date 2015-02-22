<?php

namespace PragmaRX\Tracker\Services;

use PragmaRX\Support\Config as Config;
use Illuminate\Foundation\Application;

class Authentication {

    private $config;

    private $authentication;

    public function __construct(Config $config, Application $app)
    {
        $this->config = $config;

        $class = $this->config->get('authentication_facade');

        $this->authentication = $app->make($this->config->get('authentication_ioc_binding'));
    }

    public function check()
    {
        $method = $this->config->get('authenticated_check_method');

        return $this->authentication->{$method}();
    }

    public function user()
    {
        $method = $this->config->get('authenticated_user_method');

        return $this->authentication->{$method}();
    }

    public function getCurrentUserId()
    {
        if ($this->check())
        {
            return $this->user()->{$this->config->get('authenticated_user_id_column')};
        }

        return null;
    }

}
