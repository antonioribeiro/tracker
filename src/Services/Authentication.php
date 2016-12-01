<?php

namespace PragmaRX\Tracker\Services;

use Illuminate\Foundation\Application;
use PragmaRX\Support\Config as Config;

class Authentication
{
    private $config;

    private $authentication = [];

    public function __construct(Config $config, Application $app)
    {
        $this->config = $config;

        $this->instantiateAuthentication($app);
    }

    public function check()
    {
        return $this->executeAuthMethod($this->config->get('authenticated_check_method'));
    }

    private function executeAuthMethod($method)
    {
        foreach ($this->authentication as $auth) {
            if (is_callable([$auth, $method], true, $callable_name)) {
                if ($data = call_user_func($callable_name)) {
                    return $data;
                }
            }
        }

        return false;
    }

    /**
     * @param Application $app
     */
    private function instantiateAuthentication(Application $app)
    {
        foreach ((array) $this->config->get('authentication_ioc_binding') as $binding) {
            $this->authentication[] = $app->make($binding);
        }
    }

    public function user()
    {
        return $this->executeAuthMethod($this->config->get('authenticated_user_method'));
    }

    public function getCurrentUserId()
    {
        if ($this->check()) {
            return $this->user()->{$this->config->get('authenticated_user_id_column')};
        }
    }
}
